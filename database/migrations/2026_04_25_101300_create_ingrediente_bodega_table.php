<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock level per ingredient per warehouse
        Schema::create('ingrediente_bodega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->onDelete('cascade');
            $table->foreignId('bodega_id')->constrained('bodegas')->onDelete('cascade');
            // Negative allowed: kitchen reality means counts are approximate
            $table->decimal('stock_actual', 10, 3)->default(0);
            $table->decimal('stock_minimo', 10, 3)->default(0);
            $table->boolean('alerta_minimo')->default(false);
            $table->timestamps();

            $table->unique(['ingrediente_id', 'bodega_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingrediente_bodega');
    }
};
