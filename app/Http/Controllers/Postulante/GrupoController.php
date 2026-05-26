<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();

        if (!$postulante) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un perfil de postulante registrado.');
        }

        $asignacion = DB::table('asignacion_grupos')
            ->join('grupos', 'asignacion_grupos.grupo_id', '=', 'grupos.id')
            ->join('gestiones', 'asignacion_grupos.gestion_id', '=', 'gestiones.id')
            ->select('grupos.*', 'gestiones.anio', 'gestiones.periodo')
            ->where('asignacion_grupos.postulante_id', $postulante->id)
            ->orderBy('asignacion_grupos.id', 'desc')
            ->first();

        $horarios = collect();
        if ($asignacion) {
            $horarios = DB::table('horarios')
                ->join('materias', 'horarios.materia_id', '=', 'materias.id')
                ->join('docentes', 'horarios.docente_id', '=', 'docentes.id')
                ->join('users', 'docentes.user_id', '=', 'users.id')
                ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
                ->select('horarios.*', 'materias.nombre as materia', 'users.name as docente', 'aulas.nombre as aula')
                ->where('horarios.grupo_id', $asignacion->id)
                ->orderBy('horarios.dia')
                ->orderBy('horarios.hora_inicio')
                ->get();
        }

        return view('postulante.grupo', compact('asignacion', 'horarios', 'postulante'));
    }
}
