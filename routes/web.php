<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\CoursController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Classes
    Route::get('/classes', [ClasseController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClasseController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClasseController::class, 'store'])->name('classes.store');

    // Matières
    Route::get('/matieres', [MatiereController::class, 'manage'])->name('matieres.manage');
    Route::get('/matieres/create', [MatiereController::class, 'create'])->name('matieres.create');
    Route::post('/matieres', [MatiereController::class, 'store'])->name('matieres.store');
    Route::get('/matieres/{matiere}/edit', [MatiereController::class, 'edit'])->name('matieres.edit');
    Route::put('/matieres/{matiere}', [MatiereController::class, 'update'])->name('matieres.update');
    Route::delete('/matieres/{matiere}', [MatiereController::class, 'destroy'])->name('matieres.destroy');

    // Affectation matière ↔ classes
    Route::get('/matieres/{matiere}/affecter', [MatiereController::class, 'affecter'])->name('matieres.affecter');
    Route::post('/matieres/{matiere}/affecter', [MatiereController::class, 'storeAffectation'])->name('matieres.affecter.store');

    // Matières d’une classe
    Route::get('/classes/{classe}/matieres', [MatiereController::class, 'indexByClasse'])->name('matieres.classe');

    // Cours (contenu des matières)
    Route::get('/matieres/{matiere}/cours', [CoursController::class, 'index'])->name('cours.index');
    Route::get('/matieres/{matiere}/cours/create', [CoursController::class, 'create'])->name('cours.create');
    Route::post('/matieres/{matiere}/cours', [CoursController::class, 'store'])->name('cours.store');

    Route::get('/cours/{cours}/edit', [CoursController::class, 'edit'])->name('cours.edit');
    Route::put('/cours/{cours}', [CoursController::class, 'update'])->name('cours.update');
    Route::delete('/cours/{cours}', [CoursController::class, 'destroy'])->name('cours.destroy');
});

require __DIR__ . '/auth.php';
