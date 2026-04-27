<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo_iso2', 2)->unique();   // CL, CO, VE
            $table->string('codigo_iso3', 3)->nullable(); // CHL, COL, VEN
            $table->string('tipo_id_fiscal', 20)->nullable(); // RUT, NIT, RIF
            $table->string('moneda_codigo', 10)->nullable();  // CLP, COP, VES
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
