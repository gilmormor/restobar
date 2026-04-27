<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);                    // Razón social
            $table->string('nombre_comercial', 200)->nullable(); // Nombre de fantasía
            $table->string('tipo_id_fiscal', 20)->nullable(); // RUT, NIT, RIF, RUC
            $table->string('id_fiscal', 50)->nullable();      // 12.345.678-9
            $table->string('giro', 200)->nullable();          // Actividad económica
            $table->string('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('moneda_id')->nullable()->constrained('monedas')->nullOnDelete();
            $table->foreignId('pais_id')->nullable()->constrained('paises')->nullOnDelete();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
