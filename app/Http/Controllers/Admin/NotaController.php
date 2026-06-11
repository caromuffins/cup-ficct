<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    public function index()
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = $gestion ? DB::table('grupos')->where('gestion_id', $gestion->id)->get() : collect();
        $materias = DB::table('materias')->get();

        return view('admin.notas.index', compact('gestion', 'grupos', 'materias'));
    }

    public function getAlumnos(Request $request)
    {
        $grupo_id   = $request->grupo_id;
        $materia_id = $request->materia_id;
        $gestion    = DB::table('gestiones')->where('activa', true)->first();

        $alumnos = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.id', 'users.name', 'postulantes.ci')
            ->where('asignacion_grupos.grupo_id', $grupo_id)
            ->orderBy('users.name')
            ->get();

        $examenes = DB::table('examenes')
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion->id)
            ->get();

        // Si no existen examenes para esta materia/gestion, crearlos
        if ($examenes->isEmpty()) {
            $tipos    = ['parcial1', 'parcial2', 'final'];
            $puntajes = ['parcial1' => 30, 'parcial2' => 30, 'final' => 40];

            foreach ($tipos as $tipo) {
                DB::table('examenes')->insert([
                    'materia_id'     => $materia_id,
                    'gestion_id'     => $gestion->id,
                    'tipo'           => $tipo,
                    'puntaje_maximo' => $puntajes[$tipo],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $examenes = DB::table('examenes')
                ->where('materia_id', $materia_id)
                ->where('gestion_id', $gestion->id)
                ->get();
        }

        // Obtener notas existentes
        $notas = DB::table('notas')
            ->whereIn('postulante_id', $alumnos->pluck('id'))
            ->whereIn('examen_id', $examenes->pluck('id'))
            ->get()
            ->groupBy('postulante_id');

        return response()->json([
            'alumnos'  => $alumnos,
            'examenes' => $examenes,
            'notas'    => $notas,
        ]);
    }

    public function store(Request $request)
    {
        $gestion    = DB::table('gestiones')->where('activa', true)->first();
        $materia_id = $request->materia_id;
        $grupo_id   = $request->grupo_id;
        $notas      = $request->notas; // array[postulante_id][examen_id] = puntaje

        foreach ($notas as $postulante_id => $examenes) {

            foreach ($examenes as $examen_id => $puntaje) {
                if ($puntaje === null || $puntaje === '') continue;

                $puntaje = (float) $puntaje;
                if ($puntaje < 0) $puntaje = 0;

                $maxPuntaje = DB::table('examenes')->where('id', $examen_id)->value('puntaje_maximo');
                if ($puntaje > $maxPuntaje) $puntaje = $maxPuntaje;

                $existe = DB::table('notas')
                    ->where('postulante_id', $postulante_id)
                    ->where('examen_id', $examen_id)
                    ->exists();

                if ($existe) {
                    DB::table('notas')
                        ->where('postulante_id', $postulante_id)
                        ->where('examen_id', $examen_id)
                        ->update(['puntaje' => $puntaje, 'grupo_id' => $grupo_id, 'updated_at' => now()]);
                } else {
                    DB::table('notas')->insert([
                        'postulante_id' => $postulante_id,
                        'examen_id'     => $examen_id,
                        'puntaje'       => $puntaje,
                        'grupo_id'      => $grupo_id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            $this->calcularResultado($postulante_id, $materia_id, $gestion->id);
        }

        return back()->with('success', 'Notas guardadas correctamente.');
    }

    private function calcularResultado($postulante_id, $materia_id, $gestion_id)
    {
        $gestion = DB::table('gestiones')->where('id', $gestion_id)->first();

        $examenes = DB::table('examenes')
            ->where('materia_id', $materia_id)
            ->where('gestion_id', $gestion_id)
            ->pluck('id');

        $notas = DB::table('notas')
            ->whereIn('examen_id', $examenes)
            ->where('postulante_id', $postulante_id)
            ->pluck('puntaje');

        if ($notas->isEmpty()) return;

        $total    = $notas->sum();
        $aprobado = $total >= 60;

        $existe = DB::table('resultado_materias')
            ->where('postulante_id', $postulante_id)
            ->where('materia_id', $materia_id)
            ->exists();

        if ($existe) {
            DB::table('resultado_materias')
                ->where('postulante_id', $postulante_id)
                ->where('materia_id', $materia_id)
                ->update([
                    'total'      => $total,
                    'aprobado'   => $aprobado,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('resultado_materias')->insert([
                'postulante_id' => $postulante_id,
                'materia_id'    => $materia_id,
                'total'         => $total,
                'aprobado'      => $aprobado,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
