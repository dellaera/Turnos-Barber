<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barbero_id')->constrained()->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('estado')->default('reservado');
            $table->timestamps();

            $table->unique(['barbero_id', 'fecha', 'hora'], 'turno_unico_por_barbero');
            $table->index(['barberia_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
