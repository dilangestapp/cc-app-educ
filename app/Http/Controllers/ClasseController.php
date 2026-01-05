<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClasseController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('classes')) {
            return view('classes.index', [
                'classes' => collect(),
                'error'   => "La table 'classes' n'existe pas encore. Lance les migrations : php artisan migrate --force",
            ]);
        }

        $hasPivot = Schema::hasTable('classe_matiere');
        $hasCours = Schema::hasTable('cours');

        $q = DB::table('classes as cl')->select('cl.*');

        // ✅ nombre de matières affectées à la classe (pivot)
        if ($hasPivot) {
            $q->selectSub(function ($sub) {
                $sub->from('classe_matiere as cm')
                    ->selectRaw('COUNT(DISTINCT cm.matiere_id)')
                    ->whereColumn('cm.classe_id', 'cl.id');
            }, 'matieres_count');
        } else {
            $q->selectRaw('0 as matieres_count');
        }

        // ✅ nombre de cours de la classe
        if ($hasCours) {
            $q->selectSub(function ($sub) {
                $sub->from('cours as c')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('c.classe_id', 'cl.id');
            }, 'cours_count');
        } else {
            $q->selectRaw('0 as cours_count');
        }

        $classes = $q->orderBy('cl.id')->get();

        return view('classes.index', [
            'classes' => $classes,
            'error'   => null,
        ]);
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante. Lance les migrations.");

        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $data = [
            'nom' => trim((string)$request->nom),
        ];

        if (Schema::hasColumn('classes', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('classes', 'updated_at')) $data['updated_at'] = now();

        DB::table('classes')->insert($data);

        return redirect()->route('classes.index')->with('success', 'Classe créée.');
    }
}
