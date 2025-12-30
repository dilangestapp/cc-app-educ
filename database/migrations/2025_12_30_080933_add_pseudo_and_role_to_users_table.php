<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // pseudo nullable TEMPORAIREMENT
            $table->string('pseudo')->nullable()->unique()->after('id');
            $table->string('role')->default('eleve')->after('password');
        });

        // Générer des pseudos pour les utilisateurs existants
        DB::table('users')
            ->whereNull('pseudo')
            ->update([
                'pseudo' => DB::raw("concat('user_', id)")
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pseudo', 'role']);
        });
    }
};
