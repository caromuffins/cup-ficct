<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
public function index()
{
    $user = auth()->user();

    if ($user->role === 'admin') {
        $gestion = DB::table('gestiones')->where('activa', true)->first();

        $stats = [
            'total_postulantes'     => DB::table('postulantes')->count(),
            'total_habilitados'     => DB::table('postulantes')->where('estado', 'habilitado')->count(),
            'total_inscritos'       => DB::table('postulantes')->where('estado', 'inscrito')->count(),
            'total_grupos'          => $gestion ? DB::table('grupos')->where('gestion_id', $gestion->id)->count() : 0,
            'total_pagos'           => DB::table('pagos')->where('estado', 'completado')->count(),
            'pendientes_pago'       => DB::table('postulantes')->where('estado', 'pendiente')->count(),
            'requisitos_pendientes' => DB::table('requisito_postulante')->where('estado', 'pendiente')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'gestion'));
    }

    if ($user->role === 'docente') {
        return redirect()->route('docente.dashboard');
    }

    if ($user->role === 'postulante') {
        $postulante = DB::table('postulantes')->where('user_id', $user->id)->first();

        $inscripcion = $postulante
            ? DB::table('inscripciones')->where('postulante_id', $postulante->id)->orderBy('id', 'desc')->first()
            : null;

        $tieneRequisitos = $postulante
            ? DB::table('requisito_postulante')->where('postulante_id', $postulante->id)->exists()
            : false;

        $tieneGrupo = $postulante
            ? DB::table('asignacion_grupos')->where('postulante_id', $postulante->id)->exists()
            : false;

        return view('postulante.dashboard', compact('postulante', 'inscripcion', 'tieneRequisitos', 'tieneGrupo'));
    }

    return redirect('/login');
}
}