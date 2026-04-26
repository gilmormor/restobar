<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('unidad_medida', ['gramos', 'kilogramos', 'mililitros', 'litros', 'unidades']);
            $table->decimal('stock_actual', 10, 3)->default(0); // permite negativos
            $table->decimal('stock_minimo', 10, 3)->default(0);
            $table->boolean('alerta_minimo')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredientes');
    }
};
