<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ si la table existe déjà, on ne casse pas les migrations
        if (Schema::hasTable('matieres')) {
            return;
        }

        Schema::create('matieres', function (Blueprint $table) {
            $table->id();

            // Legacy (si tu l'avais déjà prévu à l'époque)
            $table->unsignedBigInteger('classe_id')->nullable();

            $table->string('nom', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
