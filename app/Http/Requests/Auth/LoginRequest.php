<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->input('login_tipo') === 'ci') {
            return [
                'ci'       => ['required', 'string'],
                'password' => ['required', 'string'],
            ];
        }

        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if ($this->input('login_tipo') === 'ci') {
            $registro = DB::table('postulantes')
                ->join('users', 'postulantes.user_id', '=', 'users.id')
                ->where('postulantes.ci', $this->input('ci'))
                ->select('users.email')
                ->first();

            if (!$registro) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'ci' => 'El CI ingresado no está registrado.',
                ]);
            }

            if (!Auth::attempt(['email' => $registro->email, 'password' => $this->input('password')], $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'ci' => trans('auth.failed'),
                ]);
            }
        } else {
            if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        if ($this->input('login_tipo') === 'ci') {
            return Str::transliterate(Str::lower($this->string('ci')) . '|' . $this->ip());
        }

        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
