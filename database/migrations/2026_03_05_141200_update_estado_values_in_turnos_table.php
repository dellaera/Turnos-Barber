<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE turnos SET estado = 'programado' WHERE estado IN ('reservado', 'confirmado')");
        DB::statement("ALTER TABLE turnos ALTER COLUMN estado SET DEFAULT 'programado'");
    }

    public function down(): void
    {
        DB::statement("UPDATE turnos SET estado = 'reservado' WHERE estado = 'programado'");
        DB::statement("ALTER TABLE turnos ALTER COLUMN estado SET DEFAULT 'reservado'");
    }
};
