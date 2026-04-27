<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Key-value store for system configuration (per-sucursal or global)
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('clave');                // 'moneda', 'impuesto_porcentaje', 'nombre_negocio'
            $table->text('valor')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();

            $table->unique(['sucursal_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
