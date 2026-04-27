<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('restrict');
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('restrict');
            $table->string('numero_factura')->nullable(); // sequential invoice number
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0); // tax (if applicable)
            $table->decimal('total', 10, 2);
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_documento')->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
