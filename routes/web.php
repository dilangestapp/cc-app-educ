<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\CoursController;

Route::get('/', function () {
    return view('welcome');
});

// ✅ ADMIN ONLY (on remet ta vue dashboard existante)
Route::get('/dashboard', function () {
    return view('dashboard'); // <-- ton dashboard qui était déjà fonctionnel
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

// ✅ DASHBOARDS SÉPARÉS
Route::view('/eleve', 'dashboards.eleve')
    ->middleware(['auth', 'verified', 'role:eleve'])
    ->name('eleve.dashboard');

Route::view('/enseignant', 'dashboards.enseignant')
    ->middleware(['auth', 'verified', 'role:enseignant'])
    ->name('enseignant.dashboard');

Route::view('/parent', 'dashboards.parent')
    ->middleware(['auth', 'verified', 'role:parent'])
    ->name('parent.dashboard');

Route::middleware('auth')->group(function () {

    // profil accessible à tous
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ Back-office => ADMIN ONLY
    Route::middleware('role:admin')->group(function () {

        Route::get('/classes', [ClasseController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ClasseController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClasseController::class, 'store'])->name('classes.store');

        Route::get('/matieres', [MatiereController::class, 'manage'])->name('matieres.manage');
        Route::get('/matieres/create', [MatiereController::class, 'create'])->name('matieres.create');
        Route::post('/matieres', [MatiereController::class, 'store'])->name('matieres.store');
        Route::get('/matieres/{matiere}/edit', [MatiereController::class, 'edit'])->name('matieres.edit');
        Route::put('/matieres/{matiere}', [MatiereController::class, 'update'])->name('matieres.update');
        Route::delete('/matieres/{matiere}', [MatiereController::class, 'destroy'])->name('matieres.destroy');

        Route::get('/matieres/{matiere}/affecter', [MatiereController::class, 'affecter'])->name('matieres.affecter');
        Route::post('/matieres/{matiere}/affecter', [MatiereController::class, 'storeAffectation'])->name('matieres.affecter.store');

        Route::get('/classes/{classe}/matieres', [MatiereController::class, 'indexByClasse'])->name('matieres.classe');

        Route::get('/matieres/import', [MatiereController::class, 'importForm'])->name('matieres.import');
        Route::post('/matieres/import', [MatiereController::class, 'importStore'])->name('matieres.import.store');

        Route::get('/matieres/{matiere}/cours', [CoursController::class, 'index'])->name('cours.index');
        Route::get('/matieres/{matiere}/cours/create', [CoursController::class, 'create'])->name('cours.create');
        Route::post('/matieres/{matiere}/cours', [CoursController::class, 'store'])->name('cours.store');

        Route::get('/cours/{cours}/edit', [CoursController::class, 'edit'])->name('cours.edit');
        Route::put('/cours/{cours}', [CoursController::class, 'update'])->name('cours.update');
        Route::delete('/cours/{cours}', [CoursController::class, 'destroy'])->name('cours.destroy');

        Route::get('/matieres/{matiere}/cours/import', [CoursController::class, 'importForm'])->name('cours.import');
        Route::post('/matieres/{matiere}/cours/import', [CoursController::class, 'importStore'])->name('cours.import.store');
    });
});

require __DIR__ . '/auth.php';
