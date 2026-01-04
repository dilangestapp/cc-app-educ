<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('matieres') || !Schema::hasColumn('matieres', 'classe_id')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `matieres` MODIFY `classe_id` BIGINT UNSIGNED NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE matieres ALTER COLUMN classe_id DROP NOT NULL');
            return;
        }
    }

    public function down(): void
    {
        // volontairement soft : on ne remet pas NOT NULL automatiquement (risque de casser les données)
    }
};
