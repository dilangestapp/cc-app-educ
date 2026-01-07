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
            'pseudo' => ['required', 'string', 'max:60'],
            'type_compte' => ['required', 'string', 'in:eleve,enseignant,parent,admin'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->pseudo, // on évite le nom réel
            'password' => Hash::make($request->password),
        ];

        // email optionnel
        if (!empty($request->email)) {
            $data['email'] = $request->email;
        } else {
            // si ton users.email est NOT NULL, ça plantera. Dans ce cas, dis-moi et on adapte la migration.
            $data['email'] = 'pseudo_' . time() . '@local.test';
        }

        // Ajouter pseudo/type_compte seulement si colonnes existantes
        if (Schema::hasColumn('users', 'pseudo')) {
            $data['pseudo'] = $request->pseudo;
        }
        if (Schema::hasColumn('users', 'type_compte')) {
            $data['type_compte'] = $request->type_compte;
        }

        $user = User::create($data);

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
