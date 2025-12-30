<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ====== matieres ======
        if (Schema::hasTable('matieres')) {
            $hasCreated = Schema::hasColumn('matieres', 'created_at');
            $hasUpdated = Schema::hasColumn('matieres', 'updated_at');

            if (!$hasCreated || !$hasUpdated) {
                Schema::table('matieres', function (Blueprint $table) use ($hasCreated, $hasUpdated) {
                    if (!$hasCreated) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!$hasUpdated) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }

        // ====== classe_matiere ======
        if (Schema::hasTable('classe_matiere')) {
            $hasCreated2 = Schema::hasColumn('classe_matiere', 'created_at');
            $hasUpdated2 = Schema::hasColumn('classe_matiere', 'updated_at');

            if (!$hasCreated2 || !$hasUpdated2) {
                Schema::table('classe_matiere', function (Blueprint $table) use ($hasCreated2, $hasUpdated2) {
                    if (!$hasCreated2) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!$hasUpdated2) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // on ne drop pas en prod
    }
};
