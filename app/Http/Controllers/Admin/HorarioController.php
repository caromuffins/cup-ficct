<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{
    public function index()
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        if (!$gestion) {
            return view('admin.horarios.index', [
                'horarios' => collect(), 'grupos' => collect(), 'materias' => collect(),
                'docentes' => collect(), 'aulas' => collect(), 'gestion' => null,
            ])->with('error', 'No hay una gestión activa.');
        }

        $horarios = DB::table('horarios')
            ->join('grupos', 'horarios.grupo_id', '=', 'grupos.id')
            ->join('materias', 'horarios.materia_id', '=', 'materias.id')
            ->join('docentes', 'horarios.docente_id', '=', 'docentes.id')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
            ->select('horarios.*', 'grupos.nombre as grupo', 'grupos.turno',
                     'materias.nombre as materia', 'users.name as docente',
                     'aulas.nombre as aula')
            ->where('grupos.gestion_id', $gestion->id)
            ->orderBy('grupos.nombre')
            ->orderByRaw("CASE horarios.dia
                WHEN 'lunes' THEN 1 WHEN 'martes' THEN 2
                WHEN 'miercoles' THEN 3 WHEN 'jueves' THEN 4
                WHEN 'viernes' THEN 5 WHEN 'sabado' THEN 6 END")
            ->get();

        $grupos   = DB::table('grupos')->where('gestion_id', $gestion->id)->get();
        $materias = DB::table('materias')->get();
        $docentes = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.id', 'users.name', 'docentes.especialidad')
            ->where('docentes.estado_contratacion', 'contratado')
            ->get();
        $aulas = DB::table('aulas')->get();

        return view('admin.horarios.index', compact('horarios', 'grupos', 'materias', 'docentes', 'aulas', 'gestion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_id'    => 'required|exists:grupos,id',
            'materia_id'  => 'required|exists:materias,id',
            'docente_id'  => 'required|exists:docentes,id',
            'aula_id'     => 'required|exists:aulas,id',
            'dia'         => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'hora_inicio' => 'required',
            'hora_fin'    => 'required|after:hora_inicio',
        ]);

        // Verificar que el docente enseña esta materia (área coincide)
        $docente = DB::table('docentes')->where('id', $request->docente_id)->first();
        $materia = DB::table('materias')->where('id', $request->materia_id)->first();

        if ($docente && $materia && strtolower($docente->especialidad) !== strtolower($materia->nombre)) {
            return back()
                ->with('error', "El docente es de '{$docente->especialidad}' y no puede dictar '{$materia->nombre}'.")
                ->withInput();
        }

        // Verificar cruce de horarios del docente (overlapping intervals: A < D AND B > C)
        $cruceDocente = DB::table('horarios')
            ->where('docente_id', $request->docente_id)
            ->where('dia', $request->dia)
            ->where('hora_inicio', '<', $request->hora_fin)
            ->where('hora_fin', '>', $request->hora_inicio)
            ->exists();

        if ($cruceDocente) {
            return back()->with('error', 'El docente ya tiene un horario asignado en ese día y hora.')
                         ->withInput();
        }

        // Verificar cruce de horarios del aula
        $cruceAula = DB::table('horarios')
            ->where('aula_id', $request->aula_id)
            ->where('dia', $request->dia)
            ->where('hora_inicio', '<', $request->hora_fin)
            ->where('hora_fin', '>', $request->hora_inicio)
            ->exists();

        if ($cruceAula) {
            return back()->with('error', 'El aula ya está ocupada en ese día y hora.')
                         ->withInput();
        }

        // Verificar que el docente no exceda max_grupos
        $gruposAsignados = DB::table('asignacion_docentes')
            ->where('docente_id', $request->docente_id)
            ->distinct('grupo_id')
            ->count('grupo_id');

        $maxGrupos = DB::table('docentes')
            ->where('id', $request->docente_id)
            ->value('max_grupos');

        if ($gruposAsignados >= $maxGrupos) {
            return back()->with('error', 'El docente ya alcanzó el máximo de grupos permitidos ('.$maxGrupos.').')
                         ->withInput();
        }

        DB::table('horarios')->insert([
            'grupo_id'    => $request->grupo_id,
            'materia_id'  => $request->materia_id,
            'docente_id'  => $request->docente_id,
            'aula_id'     => $request->aula_id,
            'dia'         => $request->dia,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin'    => $request->hora_fin,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Registrar asignacion docente si no existe
        $gestion_id = DB::table('grupos')->where('id', $request->grupo_id)->value('gestion_id');

        DB::table('asignacion_docentes')->insertOrIgnore([
            'docente_id'  => $request->docente_id,
            'grupo_id'    => $request->grupo_id,
            'materia_id'  => $request->materia_id,
            'gestion_id'  => $gestion_id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Horario asignado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('horarios')->where('id', $id)->delete();
        return back()->with('success', 'Horario eliminado correctamente.');
    }
}
