<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PagoController extends Controller
{
    private function getAccessToken()
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_CLIENT_SECRET')
            )->asForm()->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        return $response->json()['access_token'];
    }

    public function crear(Request $request)
    {
        $user       = auth()->user();
        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();
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

        $gestion = DB::table('gestiones')->where('id', $inscripcion->gestion_id)->first();
        $monto   = number_format($gestion->monto_inscripcion, 2, '.', '');

        $accessToken = $this->getAccessToken();

        $response = Http::withoutVerifying()
            ->withToken($accessToken)
            ->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value'         => $monto,
                    ],
                    'description' => 'Inscripcion CUP FICCT UAGRM',
                ]],
                'application_context' => [
                    'return_url' => route('postulante.pago.exitoso'),
                    'cancel_url' => route('postulante.pago.cancelado'),
                ],
            ]);

        $order = $response->json();

        if (!isset($order['id'])) {
            return back()->with('error', 'Error al crear el pago. Intenta de nuevo.');
        }

        DB::table('pagos')->insert([
            'inscripcion_id' => $inscripcion->id,
            'monto'          => $monto,
            'moneda'         => 'USD',
            'metodo'         => 'paypal',
            'estado'         => 'pendiente',
            'transaccion_id' => $order['id'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $approvalUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'];

        return redirect($approvalUrl);
    }

    public function exitoso(Request $request)
    {
        $token       = $request->get('token');
        $accessToken = $this->getAccessToken();

        $response = Http::withoutVerifying()
            ->withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$token}/capture");

        $order = $response->json();

        if ($order['status'] === 'COMPLETED') {
            $pago = DB::table('pagos')->where('transaccion_id', $token)->first();

            DB::table('pagos')->where('transaccion_id', $token)->update([
                'estado'     => 'completado',
                'fecha_pago' => now(),
                'updated_at' => now(),
            ]);

            DB::table('inscripciones')->where('id', $pago->inscripcion_id)->update([
                'estado'     => 'pagada',
                'updated_at' => now(),
            ]);

            $inscripcion = DB::table('inscripciones')->where('id', $pago->inscripcion_id)->first();

            DB::table('postulantes')->where('id', $inscripcion->postulante_id)->update([
                'estado'     => 'habilitado',
                'updated_at' => now(),
            ]);

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
