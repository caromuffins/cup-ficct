<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = DB::table('grupos')->where('gestion_id', $gestion->id)->get();
        $materias = DB::table('materias')->get();
        $carreras = DB::table('carreras')->get();
        $docentes = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.id', 'users.name')
            ->get();

        return view('admin.reportes.index', compact('gestion', 'grupos', 'materias', 'carreras', 'docentes'));
    }

    public function aprobados(Request $request)
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $query = DB::table('admisiones')
            ->join('postulantes', 'admisiones.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->join('asignacion_grupos', function($join) use ($gestion) {
                $join->on('postulantes.id', '=', 'asignacion_grupos.postulante_id')
                     ->where('asignacion_grupos.gestion_id', $gestion->id);
            })
            ->join('grupos', 'asignacion_grupos.grupo_id', '=', 'grupos.id')
            ->leftJoin('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
            ->select(
                'users.name', 'postulantes.ci', 'postulantes.sexo',
                'grupos.nombre as grupo', 'grupos.turno',
                'admisiones.promedio_general', 'admisiones.admitido',
                'admisiones.opcion_asignada', 'carreras.nombre as carrera'
            );

        if ($request->grupo_id) {
            $query->where('grupos.id', $request->grupo_id);
        }
        if ($request->filled('admitido')) {
            $query->where('admisiones.admitido', $request->admitido === '1');
        }

        $datos = $query->orderByDesc('admisiones.promedio_general')->get();

        $estadisticas = [
            'total'        => $datos->count(),
            'admitidos'    => $datos->where('admitido', true)->count(),
            'no_admitidos' => $datos->where('admitido', false)->count(),
            'promedio'     => round($datos->avg('promedio_general'), 2),
        ];

        $grupos = DB::table('grupos')->where('gestion_id', $gestion->id)->get();

        return view('admin.reportes.aprobados', compact('datos', 'estadisticas', 'grupos', 'gestion', 'request'));
    }

    public function docentes(Request $request)
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $query = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->leftJoin('asignacion_docentes', 'docentes.id', '=', 'asignacion_docentes.docente_id')
            ->leftJoin('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
            ->leftJoin('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
            ->select(
                'docentes.id', 'users.name', 'docentes.especialidad',
                'docentes.titulo_profesional', 'docentes.tiene_maestria',
                'docentes.tiene_diplomado', 'docentes.estado_contratacion',
                DB::raw('COUNT(DISTINCT asignacion_docentes.grupo_id) as total_grupos'),
                DB::raw("STRING_AGG(DISTINCT grupos.nombre, ', ') as grupos_asignados"),
                DB::raw("STRING_AGG(DISTINCT materias.nombre, ', ') as materias_asignadas")
            )
            ->groupBy('docentes.id', 'users.name', 'docentes.especialidad',
                      'docentes.titulo_profesional', 'docentes.tiene_maestria',
                      'docentes.tiene_diplomado', 'docentes.estado_contratacion');

        if ($request->estado_contratacion) {
            $query->where('docentes.estado_contratacion', $request->estado_contratacion);
        }

        $datos = $query->get();

        return view('admin.reportes.docentes', compact('datos', 'gestion', 'request'));
    }

    public function exportarAprobados(Request $request)
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $datos = DB::table('admisiones')
            ->join('postulantes', 'admisiones.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->join('asignacion_grupos', function($join) use ($gestion) {
                $join->on('postulantes.id', '=', 'asignacion_grupos.postulante_id')
                     ->where('asignacion_grupos.gestion_id', $gestion->id);
            })
            ->join('grupos', 'asignacion_grupos.grupo_id', '=', 'grupos.id')
            ->leftJoin('carreras', 'admisiones.carrera_asignada_id', '=', 'carreras.id')
            ->select(
                'users.name as Nombre', 'postulantes.ci as CI',
                'grupos.nombre as Grupo', 'grupos.turno as Turno',
                'admisiones.promedio_general as Promedio',
                DB::raw("CASE WHEN admisiones.admitido THEN 'Admitido' ELSE 'No Admitido' END as Estado"),
                'carreras.nombre as Carrera',
                DB::raw("COALESCE(admisiones.opcion_asignada, '—') as Opcion")
            )
            ->orderByDesc('admisiones.promedio_general')
            ->get();

        $formato = $request->formato ?? 'excel';

        if ($formato === 'excel') {
            return $this->exportarExcel($datos, 'reporte_aprobados_'.$gestion->anio);
        }

        return $this->exportarPDF($datos, 'Reporte de Aprobados', $gestion);
    }

    public function exportarDocentes(Request $request)
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $datos = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->leftJoin('asignacion_docentes', 'docentes.id', '=', 'asignacion_docentes.docente_id')
            ->select(
                'users.name as Nombre',
                'docentes.especialidad as Especialidad',
                'docentes.titulo_profesional as Titulo',
                DB::raw("CASE WHEN docentes.tiene_maestria THEN 'Si' ELSE 'No' END as Maestria"),
                DB::raw("CASE WHEN docentes.tiene_diplomado THEN 'Si' ELSE 'No' END as Diplomado"),
                'docentes.estado_contratacion as Estado',
                DB::raw('COUNT(DISTINCT asignacion_docentes.grupo_id) as Grupos')
            )
            ->groupBy('docentes.id', 'users.name', 'docentes.especialidad',
                      'docentes.titulo_profesional', 'docentes.tiene_maestria',
                      'docentes.tiene_diplomado', 'docentes.estado_contratacion')
            ->get();

        $formato = $request->formato ?? 'excel';

        if ($formato === 'excel') {
            return $this->exportarExcel($datos, 'reporte_docentes_'.$gestion->anio);
        }

        return $this->exportarPDF($datos, 'Reporte de Docentes', $gestion);
    }

    private function exportarExcel($datos, $nombre)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'.csv"',
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            if ($datos->isNotEmpty()) {
                fputcsv($file, array_keys((array) $datos->first()));
                foreach ($datos as $row) {
                    fputcsv($file, (array) $row);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportarPDF($datos, $titulo, $gestion)
    {
        $html = view('admin.reportes.pdf', compact('datos', 'titulo', 'gestion'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="reporte.html"');
    }
}
