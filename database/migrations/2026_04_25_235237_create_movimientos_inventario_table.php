<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']); // entrada=compra, salida=venta, ajuste=sincerar
            $table->decimal('cantidad', 10, 3);
            $table->decimal('stock_anterior', 10, 3);
            $table->decimal('stock_nuevo', 10, 3);
            $table->string('motivo')->nullable();
            $table->unsignedBigInteger('empleado_id')->nullable(); // sin FK por orden de migración
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
