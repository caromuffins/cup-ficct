<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    /**
     * Muestra el listado de logs en la bitácora con soporte para filtros de búsqueda.
     */
    public function index(Request $request)
    {
        $query = Bitacora::with('user');

        // Filtrar por búsqueda de usuario (nombre o correo electrónico)
        if ($request->filled('usuario')) {
            $search = $request->input('usuario');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtrar por módulo específico
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->input('modulo'));
        }

        // Filtrar por acción específica
        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }

        // Filtrar por fecha de inicio (desde)
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }

        // Filtrar por fecha de fin (hasta)
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        // Obtener la paginación con query strings para mantener los filtros al cambiar de página
        $logs = $query->orderBy('created_at', 'desc')
                      ->paginate(25)
                      ->withQueryString();

        // Obtener opciones únicas registradas en la base de datos para los selectores de filtros
        $modulos = DB::table('bitacora')
            ->select('modulo')
            ->whereNotNull('modulo')
            ->orderBy('modulo')
            ->groupBy('modulo')
            ->pluck('modulo');

        $acciones = DB::table('bitacora')
            ->select('accion')
            ->whereNotNull('accion')
            ->orderBy('accion')
            ->groupBy('accion')
            ->pluck('accion');

        return view('admin.bitacora.index', compact('logs', 'modulos', 'acciones'));
    }
}
