<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-branch plate configuration: price override, availability, active status
        // If a plate has no record here for a given sucursal, it uses global defaults
        Schema::create('plato_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plato_id')->constrained('platos')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            // null = use platos.precio (global price)
            $table->decimal('precio', 10, 2)->nullable();
            $table->boolean('disponible')->default(true);
            $table->string('motivo_no_disponible')->nullable();
            $table->boolean('activo')->default(true); // whether branch offers this plate at all
            $table->timestamps();

            $table->unique(['plato_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plato_sucursal');
    }
};
