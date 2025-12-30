<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classe_matiere', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('matiere_id');

            $table->timestamps();

            $table->foreign('classe_id')
                  ->references('id')->on('classes')
                  ->onDelete('cascade');

            $table->foreign('matiere_id')
                  ->references('id')->on('matieres')
                  ->onDelete('cascade');

            $table->unique(['classe_id', 'matiere_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classe_matiere');
    }
};
