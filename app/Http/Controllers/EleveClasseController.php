<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EleveClasseController extends Controller
{
    public function edit(Request $request)
    {
        $classes = [];

        if (Schema::hasTable('classes')) {
            $labelCol = Schema::hasColumn('classes', 'nom') ? 'nom' : (Schema::hasColumn('classes', 'name') ? 'name' : 'id');

            $classes = DB::table('classes')
                ->select('id', $labelCol.' as label')
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('eleves.classe', [
            'classes' => $classes,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'classe_id' => ['required', 'integer'],
        ]);

        // Vérifier que la classe existe
        if (!Schema::hasTable('classes') || !DB::table('classes')->where('id', $request->classe_id)->exists()) {
            return back()->withErrors(['classe_id' => 'Classe invalide.'])->withInput();
        }

        $user = $request->user();
        $user->classe_id = (int) $request->classe_id;
        $user->save();

        return redirect()->route('eleve.dashboard')->with('success', 'Classe enregistrée avec succès.');
    }
}
