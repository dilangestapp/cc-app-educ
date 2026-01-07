<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pseudo'      => ['required','string','min:3','max:60','alpha_dash','unique:users,pseudo'],
            'type_compte' => ['required','in:eleve,enseignant,admin,parent'],
            'password'    => ['required','confirmed', Rules\Password::defaults()],
        ]);

        $pseudo = trim((string)$request->pseudo);
        $type   = (string)$request->type_compte;

        // Email interne (pas saisi par l’utilisateur)
        $email = strtolower($pseudo) . '@cc-app-educ.local';

        $user = new User();
        $user->forceFill([
            'name'        => $pseudo,
            'email'       => $email,
            'password'    => Hash::make($request->password),
            'pseudo'      => $pseudo,
            'type_compte' => $type,
        ]);

        // ✅ pour passer middleware "verified"
        if (Schema::hasColumn('users', 'email_verified_at')) {
            $user->email_verified_at = now();
        }

        $user->save();

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
