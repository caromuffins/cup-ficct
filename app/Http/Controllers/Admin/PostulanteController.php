<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('postulantes')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.*', 'users.name', 'users.email');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('users.name', 'ilike', '%'.$request->search.'%')
                  ->orWhere('postulantes.ci', 'ilike', '%'.$request->search.'%')
                  ->orWhere('users.email', 'ilike', '%'.$request->search.'%');
            });
        }

        if ($request->estado) {
            $query->where('postulantes.estado', $request->estado);
        }

        $postulantes = $query->paginate(15);

        return view('admin.postulantes.index', compact('postulantes'));
    }

    public function show($id)
    {
        $postulante = DB::table('postulantes')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.*', 'users.name', 'users.email')
            ->where('postulantes.id', $id)
            ->first();

        $inscripcion = DB::table('inscripciones')
            ->where('postulante_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        $carreraPrimera = $inscripcion ? DB::table('carreras')->where('id', $inscripcion->carrera_primera_id)->first() : null;
        $carreraSegunda = $inscripcion ? DB::table('carreras')->where('id', $inscripcion->carrera_segunda_id)->first() : null;

        $requisitos = DB::table('requisito_postulante')
            ->join('requisitos', 'requisito_postulante.requisito_id', '=', 'requisitos.id')
            ->select('requisito_postulante.*', 'requisitos.nombre as requisito_nombre')
            ->where('requisito_postulante.postulante_id', $id)
            ->get();

        return view('admin.postulantes.show', compact('postulante', 'inscripcion', 'carreraPrimera', 'carreraSegunda', 'requisitos'));
    }

    public function edit($id)
    {
        $postulante = DB::table('postulantes')
            ->join('users', 'postulantes.user_id', '=', 'users.id')
            ->select('postulantes.*', 'users.name', 'users.email')
            ->where('postulantes.id', $id)
            ->first();

        return view('admin.postulantes.edit', compact('postulante'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'telefono'        => 'nullable|string|max:20',
            'colegio'         => 'nullable|string|max:255',
            'ciudad'          => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'estado'          => 'required|in:pendiente,habilitado,inscrito,admitido,rechazado',
        ]);

        $postulante = DB::table('postulantes')->where('id', $id)->first();

        DB::table('users')->where('id', $postulante->user_id)->update([
            'name'       => $request->name,
            'updated_at' => now(),
        ]);

        DB::table('postulantes')->where('id', $id)->update([
            'telefono'         => $request->telefono,
            'colegio'          => $request->colegio,
            'ciudad'           => $request->ciudad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'estado'           => $request->estado,
            'updated_at'       => now(),
        ]);

        return redirect()->route('admin.postulantes.index')
            ->with('success', 'Postulante actualizado correctamente.');
    }

    public function destroy($id)
    {
        $postulante = DB::table('postulantes')->where('id', $id)->first();

        DB::table('postulantes')->where('id', $id)->delete();
        DB::table('users')->where('id', $postulante->user_id)->delete();

        return redirect()->route('admin.postulantes.index')
            ->with('success', 'Postulante eliminado correctamente.');
    }

    public function validarRequisito(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
        ]);

        DB::table('requisito_postulante')->where('id', $id)->update([
            'estado'     => $request->estado,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Requisito actualizado correctamente.');
    }
}
