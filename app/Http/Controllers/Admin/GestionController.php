<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestionController extends Controller
{
    public function index()
    {
        $gestiones = DB::table('gestiones')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('admin.gestiones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'anio'              => 'required|integer|min:2020|max:2099',
            'periodo'           => 'required|in:primero,segundo',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'required|date|after:fecha_inicio',
            'cupo_por_carrera'  => 'required|integer|min:1',
            'monto_inscripcion' => 'required|numeric|min:0',
        ]);

        $existe = DB::table('gestiones')
            ->where('anio', $request->anio)
            ->where('periodo', $request->periodo)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'periodo' => 'Ya existe una gestión para ese año y período.',
            ]);
        }

        DB::table('gestiones')->insert([
            'anio'              => $request->anio,
            'periodo'           => $request->periodo,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin,
            'activa'            => false,
            'cupo_por_carrera'  => $request->cupo_por_carrera,
            'monto_inscripcion' => $request->monto_inscripcion,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión creada correctamente.');
    }

    public function edit($id)
    {
        $gestion = DB::table('gestiones')->where('id', $id)->first();
        abort_if(!$gestion, 404);

        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'anio'              => 'required|integer|min:2020|max:2099',
            'periodo'           => 'required|in:primero,segundo',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'required|date|after:fecha_inicio',
            'cupo_por_carrera'  => 'required|integer|min:1',
            'monto_inscripcion' => 'required|numeric|min:0',
        ]);

        $existe = DB::table('gestiones')
            ->where('anio', $request->anio)
            ->where('periodo', $request->periodo)
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'periodo' => 'Ya existe una gestión para ese año y período.',
            ]);
        }

        DB::table('gestiones')->where('id', $id)->update([
            'anio'              => $request->anio,
            'periodo'           => $request->periodo,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin,
            'cupo_por_carrera'  => $request->cupo_por_carrera,
            'monto_inscripcion' => $request->monto_inscripcion,
            'updated_at'        => now(),
        ]);

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión actualizada correctamente.');
    }

    public function destroy($id)
    {
        $gestion = DB::table('gestiones')->where('id', $id)->first();
        abort_if(!$gestion, 404);

        if ($gestion->activa) {
            return back()->with('error', 'No se puede eliminar la gestión activa.');
        }

        $tienePostulantes = DB::table('inscripciones')
            ->where('gestion_id', $id)
            ->exists();

        if ($tienePostulantes) {
            return back()->with('error', 'No se puede eliminar: la gestión tiene inscripciones asociadas.');
        }

        DB::table('gestiones')->where('id', $id)->delete();

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión eliminada correctamente.');
    }

    public function activar($id)
    {
        $gestion = DB::table('gestiones')->where('id', $id)->first();
        abort_if(!$gestion, 404);

        DB::table('gestiones')->update(['activa' => false, 'updated_at' => now()]);
        DB::table('gestiones')->where('id', $id)->update(['activa' => true, 'updated_at' => now()]);

        return back()->with('success', 'Gestión "' . $gestion->anio . ' – ' . ucfirst($gestion->periodo) . '" activada.');
    }
}
