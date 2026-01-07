<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\CoursController;

// ✅ Nouveaux contrôleurs élève/admin (cours filtrés par classe + choix classe + affectation)
use App\Http\Controllers\EleveCoursController;
use App\Http\Controllers\EleveClasseController;
use App\Http\Controllers\AdminCoursClassesController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| DASHBOARDS PRINCIPAUX
|--------------------------------------------------------------------------
*/

// ✅ ADMIN ONLY (ton dashboard existant)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

// ✅ ELEVE
Route::view('/eleve', 'dashboards.eleve')
    ->middleware(['auth', 'verified', 'role:eleve'])
    ->name('eleve.dashboard');

// ✅ ENSEIGNANT
Route::view('/enseignant', 'dashboards.enseignant')
    ->middleware(['auth', 'verified', 'role:enseignant'])
    ->name('enseignant.dashboard');

// ✅ PARENT
Route::view('/parent', 'dashboards.parent')
    ->middleware(['auth', 'verified', 'role:parent'])
    ->name('parent.dashboard');


/*
|--------------------------------------------------------------------------
| ESPACE ELEVE (avec classe obligatoire + cours filtrés par classe)
|--------------------------------------------------------------------------
*/
Route::prefix('eleve')
    ->middleware(['auth', 'verified', 'role:eleve'])
    ->group(function () {

        // ✅ Choix classe (si eleve n'a pas encore sa classe)
        Route::get('/classe', [EleveClasseController::class, 'edit'])->name('eleve.classe.edit');
        Route::post('/classe', [EleveClasseController::class, 'update'])->name('eleve.classe.update');

        // ✅ Cours élève (réels) : liste + lecture
        Route::get('/cours', [EleveCoursController::class, 'index'])->name('eleve.cours');
        Route::get('/cours/{id}', [EleveCoursController::class, 'show'])->name('eleve.cours.show');

        // ✅ Les autres pages restent cliquables (placeholder)
        Route::view('/quiz', 'eleves.quiz')->name('eleve.quiz');
        Route::view('/questions', 'eleves.questions')->name('eleve.questions');
        Route::view('/groupes', 'eleves.groupes')->name('eleve.groupes');
        Route::view('/progression', 'eleves.progression')->name('eleve.progression');
    });


/*
|--------------------------------------------------------------------------
| PROFIL (tous les comptes connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | BACK-OFFICE ADMIN (classes/matières/cours de gestion)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // ✅ Affecter un cours à des classes
        Route::get('/cours/{id}/classes', [AdminCoursClassesController::class, 'edit'])->name('admin.cours.classes.edit');
        Route::post('/cours/{id}/classes', [AdminCoursClassesController::class, 'update'])->name('admin.cours.classes.update');

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
