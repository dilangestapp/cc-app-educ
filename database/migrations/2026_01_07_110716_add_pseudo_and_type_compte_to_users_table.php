<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pseudo')) {
                $table->string('pseudo', 50)->nullable()->unique()->after('name');
            }

            if (!Schema::hasColumn('users', 'type_compte')) {
                $table->string('type_compte', 20)->default('eleve')->index()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type_compte')) {
                $table->dropColumn('type_compte');
            }
            if (Schema::hasColumn('users', 'pseudo')) {
                $table->dropColumn('pseudo');
            }
        });
    }
};
