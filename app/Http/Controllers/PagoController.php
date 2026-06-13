<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PagoController extends Controller
{
    public function crear(Request $request)
    {
        $user        = auth()->user();
        $postulante  = DB::table('postulantes')->where('user_id', $user->id)->first();

        if (!$postulante) {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'No tienes un perfil de postulante.');
        }

        $inscripcion = DB::table('inscripciones')
            ->where('postulante_id', $postulante->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$inscripcion) {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'No tienes una inscripcion activa.');
        }

        if ($inscripcion->estado === 'pagada') {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'Tu inscripcion ya esta pagada.');
        }

        // Verificar si tiene todos los requisitos obligatorios aprobados
        $requisitosObligatorios = DB::table('requisitos')
            ->where('activo', true)
            ->where('obligatorio', true)
            ->pluck('id');

        $aprobadosCount = DB::table('requisito_postulante')
            ->where('postulante_id', $postulante->id)
            ->where('inscripcion_id', $inscripcion->id)
            ->whereIn('requisito_id', $requisitosObligatorios)
            ->where('estado', 'aprobado')
            ->count();

        if ($aprobadosCount < $requisitosObligatorios->count()) {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'No puedes realizar el pago hasta que el administrador haya validado y aprobado todos tus requisitos obligatorios (Título de Bachiller y otros).');
        }

        $gestion = DB::table('gestiones')->where('id', $inscripcion->gestion_id)->first();
        $monto   = (int) round($gestion->monto_inscripcion * 100); // Stripe usa centavos

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $monto,
                    'product_data' => [
                        'name'        => 'Inscripcion CUP FICCT UAGRM',
                        'description' => "Gestion {$gestion->periodo} {$gestion->anio}",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('postulante.pago.exitoso') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('postulante.pago.cancelado'),
        ]);

        DB::table('pagos')->insert([
            'inscripcion_id' => $inscripcion->id,
            'monto'          => $gestion->monto_inscripcion,
            'moneda'         => 'USD',
            'metodo'         => 'stripe',
            'estado'         => 'pendiente',
            'transaccion_id' => $session->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect($session->url);
    }

    public function exitoso(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'Sesion de pago no encontrada.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $pago = DB::table('pagos')->where('transaccion_id', $sessionId)->first();

            if (!$pago) {
                return redirect()->route('postulante.inscripcion.index')
                    ->with('error', 'No se encontro el registro de pago.');
            }

            DB::table('pagos')->where('transaccion_id', $sessionId)->update([
                'estado'     => 'completado',
                'fecha_pago' => now(),
                'updated_at' => now(),
            ]);

            DB::table('inscripciones')->where('id', $pago->inscripcion_id)->update([
                'estado'     => 'pagada',
                'updated_at' => now(),
            ]);

            $inscripcion = DB::table('inscripciones')->where('id', $pago->inscripcion_id)->first();

            $postulante = DB::table('postulantes')->where('id', $inscripcion->postulante_id)->first();

            DB::table('postulantes')->where('id', $inscripcion->postulante_id)->update([
                'estado'     => 'habilitado',
                'updated_at' => now(),
            ]);

            // Buscar grupo con cupo disponible
            $gestion = DB::table('gestiones')->where('activa', true)->first();

            $turnoPreferido  = $postulante->turno_preferido ?? null;
            $grupoDisponible = null;

            if ($turnoPreferido) {
                $grupoDisponible = DB::table('grupos')
                    ->where('gestion_id', $gestion->id)
                    ->where('turno', $turnoPreferido)
                    ->whereRaw('cupo_actual < cupo_maximo')
                    ->orderBy('id')
                    ->first();
            }

            if (!$grupoDisponible) {
                $grupoDisponible = DB::table('grupos')
                    ->where('gestion_id', $gestion->id)
                    ->whereRaw('cupo_actual < cupo_maximo')
                    ->orderBy('id')
                    ->first();
            }

            if ($grupoDisponible) {
                $yaAsignado = DB::table('asignacion_grupos')
                    ->where('postulante_id', $postulante->id)
                    ->where('gestion_id', $gestion->id)
                    ->exists();

                if (!$yaAsignado) {
                    DB::table('asignacion_grupos')->insert([
                        'postulante_id'    => $postulante->id,
                        'grupo_id'         => $grupoDisponible->id,
                        'gestion_id'       => $gestion->id,
                        'fecha_asignacion' => now(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    DB::table('grupos')
                        ->where('id', $grupoDisponible->id)
                        ->increment('cupo_actual');
                }
            }

            return redirect()->route('postulante.inscripcion.index')
                ->with('success', 'Pago realizado correctamente. Ya estas inscrito en el CUP.');
        }

        return redirect()->route('postulante.inscripcion.index')
            ->with('error', 'El pago no pudo completarse.');
    }

    public function cancelado()
    {
        return redirect()->route('postulante.inscripcion.index')
            ->with('error', 'Pago cancelado.');
    }
}
