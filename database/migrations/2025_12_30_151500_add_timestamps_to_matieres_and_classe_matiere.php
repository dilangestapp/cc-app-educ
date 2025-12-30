<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ matieres : ajout created_at / updated_at si manquants
        if (Schema::hasTable('matieres')) {
            Schema::table('matieres', function (Blueprint $table) {
                if (!Schema::hasColumn('matieres', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('matieres', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // ✅ classe_matiere : ton controller insère aussi created_at / updated_at
        if (Schema::hasTable('classe_matiere')) {
            Schema::table('classe_matiere', function (Blueprint $table) {
                if (!Schema::hasColumn('classe_matiere', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('classe_matiere', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // On ne supprime pas ces colonnes en prod (safe)
    }
};
