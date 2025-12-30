<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Si la table existe déjà (créée avant), on ne la recrée pas
        if (Schema::hasTable('cours')) {
            return;
        }

        Schema::create('cours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('matiere_id')
                ->constrained('matieres')
                ->cascadeOnDelete();

            $table->string('titre', 255);
            $table->text('contenu')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
