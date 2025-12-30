<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page publique
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (après connexion)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------*/
Route::middleware('auth')->group(function () {

    // =========================
    // Profil utilisateur (Breeze)
    // =========================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================
    // Classes
    // =========================
    Route::get('/classes', [ClasseController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClasseController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClasseController::class, 'store'])->name('classes.store');

    // (optionnel mais conseillé) CRUD complet classes si tu l'as dans le controller
    // Route::get('/classes/{classe}', [ClasseController::class, 'show'])->name('classes.show');
    // Route::get('/classes/{classe}/edit', [ClasseController::class, 'edit'])->name('classes.edit');
    // Route::put('/classes/{classe}', [ClasseController::class, 'update'])->name('classes.update');
    // Route::delete('/classes/{classe}', [ClasseController::class, 'destroy'])->name('classes.destroy');

    // =========================
    // MATIERES — GESTION GLOBALE
    // =========================
    Route::get('/matieres', [MatiereController::class, 'manage'])->name('matieres.manage');

    Route::get('/matieres/create', [MatiereController::class, 'create'])->name('matieres.create');
    Route::post('/matieres', [MatiereController::class, 'store'])->name('matieres.store');

    Route::get('/matieres/{matiere}/edit', [MatiereController::class, 'edit'])->name('matieres.edit');
    Route::put('/matieres/{matiere}', [MatiereController::class, 'update'])->name('matieres.update');

    Route::delete('/matieres/{matiere}', [MatiereController::class, 'destroy'])->name('matieres.destroy');

    // Affectation matière ↔ classes
    Route::get('/matieres/{matiere}/affecter', [MatiereController::class, 'affecter'])->name('matieres.affecter');
    Route::post('/matieres/{matiere}/affecter', [MatiereController::class, 'storeAffectation'])->name('matieres.affecter.store');

    // =========================
    // MATIERES D’UNE CLASSE (parcours pédagogique)
    // =========================
    Route::get('/classes/{classe}/matieres', [MatiereController::class, 'indexByClasse'])->name('matieres.classe');

});

require __DIR__ . '/auth.php';
