<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasseController extends Controller
{
    public function index()
    {
        $classes = DB::table('classes')->orderBy('id')->get();
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        DB::table('classes')->insert([
            'nom' => $request->nom,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('classes.index');
    }
}
