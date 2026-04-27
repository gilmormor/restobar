<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('plato_id')->constrained('platos')->onDelete('restrict');
            $table->foreignId('impresora_id')->nullable()->constrained('impresoras')->nullOnDelete();
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);  // price at time of sale
            $table->decimal('descuento', 10, 2)->default(0);
            $table->text('notas')->nullable();           // sin cebolla, extra queso, etc.
            $table->enum('estado', ['pendiente', 'en_preparacion', 'listo', 'entregado', 'anulado'])->default('pendiente');
            $table->boolean('enviado_cocina')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
