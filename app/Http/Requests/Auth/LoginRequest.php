<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // ✅ IMPORTANT: ne plus forcer "email" sinon un pseudo échoue
        return [
            'email' => ['required', 'string'], // peut être email OU pseudo
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim((string)$this->input('email'));
        $password = (string)$this->input('password');

        $credentials = [];

        // si c'est un email valide -> email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            // sinon -> pseudo si colonne existe, sinon fallback email
            if (Schema::hasColumn('users', 'pseudo')) {
                $credentials = ['pseudo' => $login, 'password' => $password];
            } else {
                $credentials = ['email' => $login, 'password' => $password];
            }
        }

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

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
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
