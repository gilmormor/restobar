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
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->string('numero')->unique(); // número correlativo de factura
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('propina', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['emitida', 'anulada'])->default('emitida');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
