<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminElevesController extends Controller
{
    public function index()
    {
        abort_if(!Auth::check(), 403);

        // ✅ Sécurité admin (plateforme payante = accès strict)
        abort_if((Auth::user()->type_compte ?? '') !== 'admin', 403);

        abort_if(!Schema::hasTable('users'), 500, "Table users manquante.");

        $eleves = DB::table('users')
            ->where('type_compte', 'eleve')
            ->orderByDesc('id')
            ->get(['id', 'pseudo', 'name', 'type_compte', 'created_at']);

        return view('eleves.index', compact('eleves'));
    }
}
