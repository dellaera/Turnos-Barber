<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->boolean('recordatorio_24_enviado')->default(false)->after('estado');
            $table->boolean('recordatorio_1_enviado')->default(false)->after('recordatorio_24_enviado');
        });
    }

    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn(['recordatorio_24_enviado', 'recordatorio_1_enviado']);
        });
    }
};
