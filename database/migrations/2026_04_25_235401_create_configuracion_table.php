<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio');
            $table->string('logo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('moneda')->default('$');
            $table->decimal('impuesto_porcentaje', 5, 2)->default(0);
            $table->boolean('cobrar_propina')->default(false);
            $table->decimal('propina_porcentaje', 5, 2)->default(10);
            $table->boolean('inventario_negativo')->default(true); // permite stock negativo
            $table->boolean('domicilios_activo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
