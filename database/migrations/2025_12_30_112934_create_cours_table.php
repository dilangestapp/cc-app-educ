<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ FIX: Si la table "cours" existe déjà, on ne recrée pas (évite le crash Railway)
        if (Schema::hasTable('cours')) {
            return;
        }

        Schema::create('cours', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('matiere_id');
            $table->string('titre', 255);
            $table->text('contenu')->nullable();

            $table->timestamps();

            // FK (si ta table matieres existe bien)
            $table->foreign('matiere_id')
                ->references('id')
                ->on('matieres')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
