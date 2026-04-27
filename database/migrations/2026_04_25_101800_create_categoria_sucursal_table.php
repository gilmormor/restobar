<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-branch category overrides: order and active status
        Schema::create('categoria_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->integer('orden')->default(0);   // branch-specific ordering
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->unique(['categoria_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_sucursal');
    }
};
