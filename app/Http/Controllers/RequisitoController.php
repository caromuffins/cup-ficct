<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequisitoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();

        $inscripcion = DB::table('inscripciones')
            ->where('postulante_id', $postulante->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$inscripcion) {
            return redirect()->route('postulante.inscripcion.index')
                ->with('error', 'Primero debes completar tu inscripcion.');
        }

        $requisitos = DB::table('requisitos')->where('activo', true)->get();

        $requisitosEntregados = DB::table('requisito_postulante')
            ->where('postulante_id', $postulante->id)
            ->where('inscripcion_id', $inscripcion->id)
            ->get()
            ->keyBy('requisito_id');

        return view('postulante.requisitos', compact('requisitos', 'requisitosEntregados', 'inscripcion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'requisito_id' => 'required|exists:requisitos,id',
            'archivo'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user        = auth()->user();
        $postulante  = DB::table('postulantes')->where('user_id', $user->id)->first();
        $inscripcion = DB::table('inscripciones')
            ->where('postulante_id', $postulante->id)
            ->orderBy('id', 'desc')
            ->first();

        $path = $request->file('archivo')->store('requisitos', 'public');

        $existe = DB::table('requisito_postulante')
            ->where('postulante_id', $postulante->id)
            ->where('requisito_id', $request->requisito_id)
            ->where('inscripcion_id', $inscripcion->id)
            ->exists();

        if ($existe) {
            DB::table('requisito_postulante')
                ->where('postulante_id', $postulante->id)
                ->where('requisito_id', $request->requisito_id)
                ->where('inscripcion_id', $inscripcion->id)
                ->update([
                    'archivo_path'  => $path,
                    'estado'        => 'pendiente',
                    'fecha_entrega' => now(),
                    'updated_at'    => now(),
                ]);
        } else {
            DB::table('requisito_postulante')->insert([
                'postulante_id'  => $postulante->id,
                'requisito_id'   => $request->requisito_id,
                'inscripcion_id' => $inscripcion->id,
                'archivo_path'   => $path,
                'estado'         => 'pendiente',
                'fecha_entrega'  => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return back()->with('success', 'Requisito subido correctamente.');
    }
}
