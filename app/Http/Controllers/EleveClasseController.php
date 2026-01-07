<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EleveClasseController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        // ✅ Si déjà défini -> on n'autorise pas à modifier (plateforme payante)
        if (!empty($user->classe_id)) {
            return view('eleves.classe_locked');
        }

        $classes = collect();

        if (Schema::hasTable('classes')) {
            $labelCol = Schema::hasColumn('classes', 'nom') ? 'nom' : (Schema::hasColumn('classes', 'name') ? 'name' : 'id');

            $classes = DB::table('classes')
                ->select('id', DB::raw($labelCol . ' as label'))
                ->orderBy($labelCol, 'asc')
                ->get();
        }

        return view('eleves.classe', [
            'classes' => $classes,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // ✅ Interdit si déjà défini
        if (!empty($user->classe_id)) {
            return redirect()->route('eleve.dashboard')
                ->withErrors(['classe_id' => "Classe déjà définie. Contacte l'administration pour toute modification."]);
        }

        $request->validate([
            'classe_id' => ['required', 'integer'],
        ]);

        if (!Schema::hasTable('classes') || !DB::table('classes')->where('id', $request->classe_id)->exists()) {
            return back()->withErrors(['classe_id' => 'Classe invalide.'])->withInput();
        }

        $user->classe_id = (int) $request->classe_id;
        $user->save();

        return redirect()->route('eleve.dashboard')->with('success', 'Classe enregistrée.');
    }
}
