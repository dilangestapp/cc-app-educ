<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

public function index()
{
    $classes = DB::table('classes')->orderBy('id')->get();

    return view('classes.index', compact('classes'));
}

}
