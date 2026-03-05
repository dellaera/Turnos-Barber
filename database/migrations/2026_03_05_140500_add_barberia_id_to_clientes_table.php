<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('barberia_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE clientes AS c
            SET barberia_id = sub.barberia_id
            FROM (
                SELECT DISTINCT ON (turnos.cliente_id) turnos.cliente_id, turnos.barberia_id
                FROM turnos
                ORDER BY turnos.cliente_id, turnos.created_at DESC
            ) AS sub
            WHERE sub.cliente_id = c.id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barberia_id');
        });
    }
};
