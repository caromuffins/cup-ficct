<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        if (!$gestion) {
            return view('admin.grupos.index', [
                'gestion' => null, 'grupos' => collect(),
                'totalInscritos' => 0, 'sinGrupo' => 0, 'gruposNecesarios' => 0,
            ])->with('error', 'No hay una gestión activa.');
        }

        $grupos = DB::table('grupos')
            ->where('gestion_id', $gestion->id)
            ->orderBy('nombre')
            ->get();

        $totalInscritos = DB::table('postulantes')
            ->where('estado', 'habilitado')
            ->count();

        $sinGrupo = DB::table('postulantes')
            ->where('estado', 'habilitado')
            ->whereNotIn('id', function($q) use ($gestion) {
                $q->select('postulante_id')
                  ->from('asignacion_grupos')
                  ->where('gestion_id', $gestion->id);
            })->count();

        $gruposNecesarios = ceil($totalInscritos / 70);

        return view('admin.grupos.index', compact('gestion', 'grupos', 'totalInscritos', 'sinGrupo', 'gruposNecesarios'));
    }

    public function generar()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $postulantes = DB::table('postulantes')
            ->where('estado', 'habilitado')
            ->whereNotIn('id', function($q) use ($gestion) {
                $q->select('postulante_id')
                  ->from('asignacion_grupos')
                  ->where('gestion_id', $gestion->id);
            })->get();

        if ($postulantes->isEmpty()) {
            return back()->with('error', 'No hay postulantes habilitados sin grupo asignado.');
        }

        $totalGrupos = ceil($postulantes->count() / 70);
        $turnos = ['maniana', 'tarde'];
        $gruposCreados = 0;

        $ultimoGrupo = DB::table('grupos')
            ->where('gestion_id', $gestion->id)
            ->max('id');

        $numeroInicio = DB::table('grupos')
            ->where('gestion_id', $gestion->id)
            ->count() + 1;

        for ($g = 0; $g < $totalGrupos; $g++) {
            $numero = $numeroInicio + $g;

            $grupoId = DB::table('grupos')->insertGetId([
                'gestion_id'  => $gestion->id,
                'nombre'      => "Grupo $numero",
                'turno'       => $turnos[$g % 2],
                'cupo_maximo' => 70,
                'cupo_actual' => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $chunk = $postulantes->slice($g * 70, 70);

            foreach ($chunk as $postulante) {
                DB::table('asignacion_grupos')->insert([
                    'postulante_id'    => $postulante->id,
                    'grupo_id'         => $grupoId,
                    'gestion_id'       => $gestion->id,
                    'fecha_asignacion' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
                DB::table('grupos')->where('id', $grupoId)->increment('cupo_actual');
            }

            $gruposCreados++;
        }

        return back()->with('success', "$gruposCreados grupo(s) generado(s) correctamente con " . $postulantes->count() . " postulantes asignados.");
    }

    public function show($id)
    {
        $grupo = DB::table('grupos')->where('id', $id)->first();

        $postulantes = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.*', 'users.name', 'users.email')
            ->where('asignacion_grupos.grupo_id', $id)
            ->get();

        $horarios = DB::table('horarios')
            ->join('materias', 'horarios.materia_id', '=', 'materias.id')
            ->join('docentes', 'horarios.docente_id', '=', 'docentes.id')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
            ->select('horarios.*', 'materias.nombre as materia', 'users.name as docente', 'aulas.nombre as aula')
            ->where('horarios.grupo_id', $id)
            ->orderBy('horarios.dia')
            ->orderBy('horarios.hora_inicio')
            ->get();

        return view('admin.grupos.show', compact('grupo', 'postulantes', 'horarios'));
    }
}
