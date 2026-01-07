<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminCoursClassesController extends Controller
{
    private function coursTable(): string
    {
        if (Schema::hasTable('cours')) return 'cours';
        if (Schema::hasTable('courses')) return 'courses';
        return 'cours';
    }

    public function edit(Request $request, $id)
    {
        $coursTable = $this->coursTable();

        if (!Schema::hasTable($coursTable) || !Schema::hasTable('classes')) {
            abort(404);
        }

        $labelCol = Schema::hasColumn('classes', 'nom') ? 'nom' : (Schema::hasColumn('classes', 'name') ? 'name' : 'id');

        $courseTitleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
                       : (Schema::hasColumn($coursTable, 'title') ? 'title'
                       : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        $cours = DB::table($coursTable)->where('id', (int)$id)->first();
        if (!$cours) abort(404);

        $classes = DB::table('classes')->select('id', $labelCol.' as label')->orderBy('id')->get();

        $assigned = [];
        if (Schema::hasTable('cours_classes')) {
            $assigned = DB::table('cours_classes')->where('cours_id', (int)$id)->pluck('classe_id')->toArray();
        }

        $title = $courseTitleCol ? ($cours->{$courseTitleCol} ?? ('Cours #'.$cours->id)) : ('Cours #'.$cours->id);

        return view('admin.cours_classes', [
            'cours_id' => (int)$id,
            'cours_title' => $title,
            'classes' => $classes,
            'assigned' => $assigned,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'classes' => ['array'],
            'classes.*' => ['integer'],
        ]);

        if (!Schema::hasTable('cours_classes')) {
            abort(500);
        }

        $new = $request->input('classes', []);
        $new = array_values(array_unique(array_map('intval', $new)));

        DB::table('cours_classes')->where('cours_id', (int)$id)->delete();

        foreach ($new as $classeId) {
            DB::table('cours_classes')->insert([
                'cours_id' => (int)$id,
                'classe_id' => (int)$classeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.cours.classes.edit', ['id' => (int)$id])
            ->with('success', 'Affectation mise à jour.');
    }
}
