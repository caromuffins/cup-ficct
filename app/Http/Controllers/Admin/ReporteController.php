<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReporteController extends Controller
{
    public function index()
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = $gestion ? DB::table('grupos')->where('gestion_id', $gestion->id)->get() : collect();
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

        $formato = $request->formato ?? 'xlsx';
        $anio    = $gestion->anio ?? date('Y');

        return match($formato) {
            'csv'  => $this->exportarCsv($datos, 'reporte_aprobados_'.$anio),
            'pdf'  => $this->exportarPDF($datos, 'Reporte de Aprobados', $gestion),
            default => $this->exportarExcel($datos, 'reporte_aprobados_'.$anio),
        };
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

        $formato = $request->formato ?? 'xlsx';
        $anio    = $gestion->anio ?? date('Y');

        return match($formato) {
            'csv'  => $this->exportarCsv($datos, 'reporte_docentes_'.$anio),
            'pdf'  => $this->exportarPDF($datos, 'Reporte de Docentes', $gestion),
            default => $this->exportarExcel($datos, 'reporte_docentes_'.$anio),
        };
    }

    public function notas(Request $request)
    {
        $gestion  = DB::table('gestiones')->where('activa', true)->first();
        $grupos   = $gestion ? DB::table('grupos')->where('gestion_id', $gestion->id)->get() : collect();
        $materias = DB::table('materias')->get();
        $datos    = collect();
        $examenes = collect();

        if ($request->filled('grupo_id')) {
            $alumnos = DB::table('asignacion_grupos')
                ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
                ->join('users', 'postulantes.user_id', '=', 'users.id')
                ->where('asignacion_grupos.grupo_id', $request->grupo_id)
                ->select('postulantes.id as postulante_id', 'users.name', 'postulantes.ci')
                ->orderBy('users.name')
                ->get();

            if ($request->filled('materia_id')) {
                $examenes = DB::table('examenes')
                    ->where('materia_id', $request->materia_id)
                    ->orderBy('tipo')
                    ->get();

                $umbral = $examenes->sum('puntaje_maximo') * 0.51;

                $datos = $alumnos->map(function ($alumno) use ($examenes, $umbral) {
                    $notasRaw = DB::table('notas')
                        ->where('postulante_id', $alumno->postulante_id)
                        ->whereIn('examen_id', $examenes->pluck('id'))
                        ->get()->keyBy('examen_id');

                    $notasArr = [];
                    $total    = 0;
                    foreach ($examenes as $ex) {
                        $n = isset($notasRaw[$ex->id]) ? $notasRaw[$ex->id]->puntaje : null;
                        $notasArr[$ex->id] = $n;
                        $total += $n ?? 0;
                    }

                    return (object)[
                        'postulante_id' => $alumno->postulante_id,
                        'name'          => $alumno->name,
                        'ci'            => $alumno->ci,
                        'notas'         => $notasArr,
                        'total'         => $total,
                        'aprobado'      => $total >= $umbral,
                    ];
                });
            } else {
                $datos = $alumnos->map(function ($alumno) {
                    $resultados = DB::table('resultado_materias')
                        ->join('materias', 'resultado_materias.materia_id', '=', 'materias.id')
                        ->where('resultado_materias.postulante_id', $alumno->postulante_id)
                        ->select('materias.nombre as materia', 'resultado_materias.total', 'resultado_materias.aprobado')
                        ->get();

                    return (object)[
                        'postulante_id' => $alumno->postulante_id,
                        'name'          => $alumno->name,
                        'ci'            => $alumno->ci,
                        'resultados'    => $resultados,
                    ];
                });
            }
        }

        return view('admin.reportes.notas', compact('grupos', 'materias', 'datos', 'examenes', 'gestion'));
    }

    public function exportarNotas(Request $request)
    {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        if (!$request->filled('grupo_id')) {
            return redirect()->route('admin.reportes.notas');
        }

        $grupo   = DB::table('grupos')->find($request->grupo_id);
        $alumnos = DB::table('asignacion_grupos')
            ->join('postulantes', 'asignacion_grupos.postulante_id', '=', 'postulantes.id')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->where('asignacion_grupos.grupo_id', $request->grupo_id)
            ->select('postulantes.id as postulante_id', 'users.name', 'postulantes.ci')
            ->orderBy('users.name')
            ->get();

        // Armar colección plana para exportar
        if ($request->filled('materia_id')) {
            $examenes     = DB::table('examenes')->where('materia_id', $request->materia_id)->orderBy('tipo')->get();
            $puntajeTotal = $examenes->sum('puntaje_maximo');
            $materia      = DB::table('materias')->find($request->materia_id);

            $filas = collect();
            foreach ($alumnos as $alumno) {
                $notasRaw = DB::table('notas')
                    ->where('postulante_id', $alumno->postulante_id)
                    ->whereIn('examen_id', $examenes->pluck('id'))
                    ->get()->keyBy('examen_id');
                $fila  = (object)['CI' => $alumno->ci, 'Nombre' => $alumno->name];
                $total = 0;
                foreach ($examenes as $ex) {
                    $label = ucfirst(str_replace(['parcial1','parcial2'], ['Parcial 1','Parcial 2'], $ex->tipo));
                    $n     = isset($notasRaw[$ex->id]) ? $notasRaw[$ex->id]->puntaje : 0;
                    $fila->{$label} = $n;
                    $total += $n;
                }
                $fila->{'Total ('.$puntajeTotal.')'} = $total;
                $fila->{'Estado'}                   = $total >= $puntajeTotal * 0.51 ? 'Aprobado' : 'Reprobado';
                $filas->push($fila);
            }
            $titulo = 'Notas '.$grupo->nombre.' - '.($materia->nombre ?? '');
        } else {
            $materias = DB::table('materias')->get();
            $filas    = collect();
            foreach ($alumnos as $alumno) {
                $resultados = DB::table('resultado_materias')
                    ->where('postulante_id', $alumno->postulante_id)
                    ->get()->keyBy('materia_id');
                $fila = (object)['CI' => $alumno->ci, 'Nombre' => $alumno->name];
                foreach ($materias as $m) {
                    $r            = $resultados[$m->id] ?? null;
                    $fila->{$m->nombre} = $r ? $r->total : '—';
                }
                $filas->push($fila);
            }
            $titulo = 'Notas resumen - '.$grupo->nombre;
        }

        $nombre  = 'notas_'.str_replace(' ', '_', $grupo->nombre).'_'.date('Y-m-d');
        $formato = $request->formato ?? 'xlsx';

        return match($formato) {
            'csv'  => $this->exportarCsv($filas, $nombre),
            'pdf'  => $this->exportarPDF($filas, $titulo, $gestion),
            default => $this->exportarExcel($filas, $nombre),
        };
    }

    private function exportarCsv($datos, $nombre)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'.csv"',
        ];

        $callback = function() use ($datos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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

    private function exportarExcel($datos, $nombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        if ($datos->isNotEmpty()) {
            $cols    = array_keys((array) $datos->first());
            $colLetr = 'A';

            // Encabezados con fondo azul oscuro
            foreach ($cols as $col) {
                $cell = $colLetr . '1';
                $sheet->setCellValue($cell, strtoupper($col));
                $sheet->getStyle($cell)->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getColumnDimension($colLetr)->setAutoSize(true);
                $colLetr++;
            }

            // Datos
            $row = 2;
            foreach ($datos as $item) {
                $colLetr = 'A';
                foreach ((array) $item as $val) {
                    $sheet->setCellValue($colLetr . $row, $val);
                    $colLetr++;
                }
                $row++;
            }
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = $nombre . '.xlsx';
        $tmpPath  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function exportarPDF($datos, $titulo, $gestion)
    {
        $pdf = Pdf::loadView('admin.reportes.pdf', compact('datos', 'titulo', 'gestion'))
                  ->setPaper('a4', 'landscape');

        $nombre = str_replace([' ', '/'], '_', $titulo) . '.pdf';

        return $pdf->download($nombre);
    }
}
