<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();

        $inscripcion = DB::table('inscripciones')
            ->where('postulante_id', $postulante->id)
            ->orderBy('id', 'desc')
            ->first();

        $carreras = DB::table('carreras')->where('activa', true)->get();
        $gestion  = DB::table('gestiones')->where('activa', true)->first();

        if ($inscripcion && !$gestion) {
            $gestion = DB::table('gestiones')->where('id', $inscripcion->gestion_id)->first();
        }

        return view('postulante.inscripcion', compact('inscripcion', 'carreras', 'gestion', 'postulante'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'carrera_primera_id' => 'required|exists:carreras,id',
            'carrera_segunda_id' => 'required|exists:carreras,id|different:carrera_primera_id',
        ]);

        $user = auth()->user();

        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();

        if (!$postulante) {
            return back()->with('error', 'No tienes un perfil de postulante registrado.');
        }

        $gestion = DB::table('gestiones')->where('activa', 1)->first();

        if (!$gestion) {
            return back()->with('error', 'No hay una gestion activa en este momento.');
        }

        $inscripcionExiste = DB::table('inscripciones')
            ->where('postulante_id', $postulante->id)
            ->where('gestion_id', $gestion->id)
            ->exists();

        if ($inscripcionExiste) {
            return back()->with('error', 'Ya tienes una inscripcion en esta gestion.');
        }

        DB::table('inscripciones')->insert([
            'postulante_id'      => $postulante->id,
            'gestion_id'         => $gestion->id,
            'carrera_primera_id' => $request->carrera_primera_id,
            'carrera_segunda_id' => $request->carrera_segunda_id,
            'estado'             => 'pendiente',
            'fecha_inscripcion'  => now(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('postulante.requisitos.index')
            ->with('success', 'Inscripcion realizada. Ahora sube tus requisitos.');
    }
}
