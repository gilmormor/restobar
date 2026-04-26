<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domicilios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('repartidor_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->text('direccion');
            $table->text('referencia')->nullable();
            $table->enum('estado', ['pendiente', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
            $table->timestamp('entregado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domicilios');
    }
};
