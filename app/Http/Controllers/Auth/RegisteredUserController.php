<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        // Charger les classes pour le select (table classes, colonne nom)
        $classes = collect();

        if (Schema::hasTable('classes')) {
            $labelCol = Schema::hasColumn('classes', 'nom') ? 'nom' : (Schema::hasColumn('classes', 'name') ? 'name' : 'id');

            $classes = DB::table('classes')
                ->select('id', DB::raw($labelCol . ' as label'))
                ->orderBy($labelCol, 'asc')
                ->get();
        }

        return view('auth.register', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // ✅ Pour une plateforme payante : la classe d'un élève doit être fixée dès l'inscription.
        // L'élève ne pourra plus la modifier ensuite côté élève.

        $type = strtolower((string) $request->input('type_compte', 'eleve'));

        // Si eleve, on exige que la table classes existe (sinon impossible choisir)
        if ($type === 'eleve' && !Schema::hasTable('classes')) {
            return back()
                ->withErrors(['classe_id' => "Aucune classe n'est disponible. Contacte l'administration."])
                ->withInput();
        }

        $request->validate([
            'pseudo' => ['required', 'string', 'max:60'],
            'type_compte' => ['required', 'string', 'in:eleve,enseignant,parent,admin'],

            // ✅ classe obligatoire UNIQUEMENT si eleve
            'classe_id' => [
                Rule::requiredIf(fn () => strtolower((string) $request->input('type_compte')) === 'eleve'),
                'nullable',
                'integer',
                // existe dans classes.id
                Rule::exists('classes', 'id'),
            ],

            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->pseudo,
            'password' => Hash::make($request->password),
        ];

        // Email optionnel
        if (!empty($request->email)) {
            $data['email'] = $request->email;
        } else {
            $data['email'] = 'pseudo_' . time() . '@local.test';
        }

        // pseudo/type_compte
        if (Schema::hasColumn('users', 'pseudo')) {
            $data['pseudo'] = $request->pseudo;
        }
        if (Schema::hasColumn('users', 'type_compte')) {
            $data['type_compte'] = $type;
        }

        // ✅ classe_id (UNIQUEMENT eleve)
        if ($type === 'eleve' && Schema::hasColumn('users', 'classe_id')) {
            $data['classe_id'] = (int) $request->classe_id;
        }

        $user = User::create($data);

        event(new Registered($user));
        Auth::login($user);

        // ✅ Redirection par type_compte (propre)
        return redirect()->route($this->homeRouteName($user));
    }

    private function homeRouteName(User $user): string
    {
        $type = strtolower((string) ($user->type_compte ?? 'eleve'));

        return match ($type) {
            'admin' => 'dashboard',
            'enseignant' => 'enseignant.dashboard',
            'parent' => 'parent.dashboard',
            default => 'eleve.dashboard',
        };
    }
}
