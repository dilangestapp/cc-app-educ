<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cours_classes')) {
            Schema::create('cours_classes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cours_id')->index();
                $table->unsignedBigInteger('classe_id')->index();
                $table->timestamps();

                $table->unique(['cours_id', 'classe_id'], 'cours_classes_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cours_classes');
    }
};
