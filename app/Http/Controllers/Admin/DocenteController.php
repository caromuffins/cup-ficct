<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.*', 'users.name', 'users.email');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('users.name', 'ilike', '%'.$request->search.'%')
                  ->orWhere('users.email', 'ilike', '%'.$request->search.'%');
            });
        }

        if ($request->estado) {
            $query->where('docentes.estado_contratacion', $request->estado);
        }

        $docentes = $query->paginate(15);

        return view('admin.docentes.index', compact('docentes'));
    }

    public function create()
    {
        return view('admin.docentes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|min:8',
            'especialidad'         => 'nullable|string|max:255',
            'titulo_profesional'   => 'nullable|string|max:255',
            'tiene_maestria'       => 'boolean',
            'area_maestria'        => 'nullable|string|max:255',
            'tiene_diplomado'      => 'boolean',
            'area_diplomado'       => 'nullable|string|max:255',
            'estado_contratacion'  => 'required|in:pendiente,contratado,rechazado',
        ]);

        $user = DB::table('users')->insertGetId([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'docente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('docentes')->insert([
            'user_id'              => $user,
            'especialidad'         => $request->especialidad,
            'titulo_profesional'   => $request->titulo_profesional,
            'tiene_maestria'       => $request->boolean('tiene_maestria'),
            'area_maestria'        => $request->area_maestria,
            'tiene_diplomado'      => $request->boolean('tiene_diplomado'),
            'area_diplomado'       => $request->area_diplomado,
            'estado_contratacion'  => $request->estado_contratacion,
            'max_grupos'           => 4,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente registrado correctamente.');
    }

    public function show($id)
    {
        $docente = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.*', 'users.name', 'users.email')
            ->where('docentes.id', $id)
            ->first();

        $grupos = DB::table('asignacion_docentes')
            ->join('grupos', 'asignacion_docentes.grupo_id', '=', 'grupos.id')
            ->join('materias', 'asignacion_docentes.materia_id', '=', 'materias.id')
            ->select('grupos.nombre as grupo', 'grupos.turno', 'materias.nombre as materia')
            ->where('asignacion_docentes.docente_id', $id)
            ->distinct()
            ->get();

        return view('admin.docentes.show', compact('docente', 'grupos'));
    }

    public function edit($id)
    {
        $docente = DB::table('docentes')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.*', 'users.name', 'users.email')
            ->where('docentes.id', $id)
            ->first();

        return view('admin.docentes.edit', compact('docente'));
    }

    public function update(Request $request, $id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first();

        $request->validate([
            'name'                => 'required|string|max:255',
            'especialidad'        => 'nullable|string|max:255',
            'titulo_profesional'  => 'nullable|string|max:255',
            'tiene_maestria'      => 'boolean',
            'area_maestria'       => 'nullable|string|max:255',
            'tiene_diplomado'     => 'boolean',
            'area_diplomado'      => 'nullable|string|max:255',
            'estado_contratacion' => 'required|in:pendiente,contratado,rechazado',
            'max_grupos'          => 'required|integer|min:1|max:4',
        ]);

        DB::table('users')->where('id', $docente->user_id)->update([
            'name'       => $request->name,
            'updated_at' => now(),
        ]);

        DB::table('docentes')->where('id', $id)->update([
            'especialidad'        => $request->especialidad,
            'titulo_profesional'  => $request->titulo_profesional,
            'tiene_maestria'      => $request->boolean('tiene_maestria'),
            'area_maestria'       => $request->area_maestria,
            'tiene_diplomado'     => $request->boolean('tiene_diplomado'),
            'area_diplomado'      => $request->area_diplomado,
            'estado_contratacion' => $request->estado_contratacion,
            'max_grupos'          => $request->max_grupos,
            'updated_at'          => now(),
        ]);

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $docente = DB::table('docentes')->where('id', $id)->first();
        DB::table('docentes')->where('id', $id)->delete();
        DB::table('users')->where('id', $docente->user_id)->delete();

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente eliminado correctamente.');
    }
}
