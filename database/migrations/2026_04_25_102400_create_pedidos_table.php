<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            $table->foreignId('mesa_id')->nullable()->constrained('mesas')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict'); // who took the order
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->enum('tipo', ['mesa', 'domicilio', 'para_llevar'])->default('mesa');
            $table->enum('estado', ['abierto', 'en_cocina', 'listo', 'cerrado', 'anulado'])->default('abierto');
            $table->integer('num_personas')->default(1);
            $table->text('notas')->nullable();
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
