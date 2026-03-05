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
        Schema::table('barberias', function (Blueprint $table) {
            $table->string('logo_url')->nullable();
            $table->string('color_primario')->nullable();
            $table->string('color_secundario')->nullable();
            $table->text('mensaje_bienvenida')->nullable();
            $table->text('informacion_contacto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barberias', function (Blueprint $table) {
            $table->dropColumn([
                'logo_url',
                'color_primario',
                'color_secundario',
                'mensaje_bienvenida',
                'informacion_contacto',
            ]);
        });
    }
};
