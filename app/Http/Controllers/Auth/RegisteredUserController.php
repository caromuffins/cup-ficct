<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|confirmed|min:8',
            'ci'               => 'required|string|unique:postulantes,ci',
            'fecha_nacimiento' => 'required|date',
            'telefono'         => 'required|string|max:20',
            'colegio'          => 'required|string|max:255',
            'ciudad'           => 'required|string|max:100',
            'sexo'             => 'required|in:M,F',
            'direccion'        => 'required|string|max:255',
            'turno_preferido'  => 'nullable|in:maniana,tarde',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'postulante',
        ]);

        \Illuminate\Support\Facades\DB::table('postulantes')->insert([
            'user_id'          => $user->id,
            'ci'               => $request->ci,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono'         => $request->telefono,
            'colegio'          => $request->colegio,
            'ciudad'           => $request->ciudad,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'estado'           => 'pendiente',
            'turno_preferido'  => $request->turno_preferido,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
