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
        $stats = [
            'postulantes' => DB::table('postulantes')->count(),
            'grupos'      => DB::table('grupos')->count(),
            'docentes'    => DB::table('docentes')->count(),
            'admitidos'   => DB::table('admisiones')->where('admitido', true)->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    if ($user->role === 'docente') {
        return view('docente.dashboard');
    }

    if ($user->role === 'postulante') {
        return view('postulante.dashboard');
    }

    return redirect('/login');
}
}