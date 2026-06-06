<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    public function index()
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = DB::table('grupos')->where('gestion_id', $gestion->id)->get();
        $materias = DB::table('materias')->get();
        $carreras = DB::table('carreras')->get();

        return view('admin.consultas.index', compact('gestion', 'grupos', 'materias', 'carreras'));
    }

    public function ejecutar(Request $request)
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = DB::table('grupos')->where('gestion_id', $gestion->id)->get();
        $materias = DB::table('materias')->get();
        $carreras = DB::table('carreras')->get();

        $resultados = DB::table('postulantes')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->join('asignacion_grupos', function($join) use ($gestion) {
                $join->on('postulantes.id', '=', 'asignacion_grupos.postulante_id')
                     ->where('asignacion_grupos.gestion_id', $gestion->id);
            })
            ->join('grupos', 'asignacion_grupos.grupo_id', '=', 'grupos.id')
            ->leftJoin('admisiones', 'postulantes.id', '=', 'admisiones.postulante_id')
            ->leftJoin('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
            ->select(
                'users.name as nombre',
                'postulantes.id',
                'postulantes.ci',
                'postulantes.sexo',
                'postulantes.ciudad',
                'grupos.nombre as grupo',
                'grupos.turno',
                'admisiones.admitido',
                'carreras.nombre as carrera_asignada'
            )
            ->when($request->grupo_id, fn($q) => $q->where('grupos.id', $request->grupo_id))
            ->when($request->sexo, fn($q) => $q->where('postulantes.sexo', $request->sexo))
            ->when($request->filled('admitido'), fn($q) => $q->where('admisiones.admitido', $request->admitido === '1'))
            ->when($request->carrera_id, fn($q) => $q->where('admisiones.carrera_asignada_id', $request->carrera_id))
            ->get();

        // Agregar promedio y materias aprobadas en PHP
        $resultados = $resultados->map(function($r) {
            $r->materias_aprobadas = DB::table('resultado_materias')
                ->where('postulante_id', $r->id)
                ->where('aprobado', true)
                ->count();

            $promedio = DB::table('resultado_materias')
                ->where('postulante_id', $r->id)
                ->avg('total');

            $r->promedio = $promedio ? round($promedio, 2) : null;
            return $r;
        });

        // Aplicar filtros de promedio en PHP
        if ($request->promedio_min) {
            $resultados = $resultados->filter(fn($r) => $r->promedio >= $request->promedio_min);
        }
        if ($request->promedio_max) {
            $resultados = $resultados->filter(fn($r) => $r->promedio <= $request->promedio_max);
        }

        $resultados = $resultados->sortByDesc('promedio')->values();

        $estadisticas = [
            'total'     => $resultados->count(),
            'admitidos' => $resultados->where('admitido', true)->count(),
            'promedio'  => round($resultados->avg('promedio'), 2),
            'masculino' => $resultados->where('sexo', 'M')->count(),
            'femenino'  => $resultados->where('sexo', 'F')->count(),
        ];

        return view('admin.consultas.index', compact(
            'gestion', 'grupos', 'materias', 'carreras',
            'resultados', 'estadisticas', 'request'
        ));
    }
}
