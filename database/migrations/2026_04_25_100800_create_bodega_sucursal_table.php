<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bodega_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bodega_id')->constrained('bodegas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->boolean('es_principal')->default(false); // main warehouse for this branch
            $table->timestamps();

            $table->unique(['bodega_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bodega_sucursal');
    }
};
