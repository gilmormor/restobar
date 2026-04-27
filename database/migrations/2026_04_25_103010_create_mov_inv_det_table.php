<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Líneas del movimiento de inventario
        Schema::create('mov_inv_det', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mov_inv_id')->constrained('mov_inv')->onDelete('restrict');
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->onDelete('restrict');
            $table->foreignId('bodega_id')->constrained('bodegas')->onDelete('restrict');
            // tipo_id en el detalle permite que una sola cabecera tenga líneas de tipos distintos
            // (útil en transferencias: una línea es salida de bodega A, otra entrada a bodega B)
            $table->foreignId('tipo_id')->constrained('mov_inv_tipos')->onDelete('restrict');

            // Cantidad expresada en la unidad que el usuario ingresa (ej: gramos, litros)
            $table->decimal('cantidad', 10, 3);
            $table->string('unidad_uso', 20)->comment('Unidad en que se expresa la cantidad');
            // Factor para convertir unidad_uso → unidad_stock del ingrediente
            // Ej: unidad_stock=kg, unidad_uso=g → factor=0.001
            $table->decimal('factor_conv', 12, 6)->default(1.000000);
            // Cantidad ya convertida a unidad_stock — campo calculado que se guarda para eficiencia
            // cantidad_stock = cantidad * factor_conv
            $table->decimal('cantidad_stock', 10, 3)
                ->comment('Cantidad en unidad_stock = cantidad * factor_conv');

            $table->decimal('costo_unitario', 10, 4)->default(0)
                ->comment('Costo por unidad de unidad_stock al momento del movimiento');

            // Snapshots del stock antes y después — para auditoría y recálculo rápido
            $table->decimal('stock_anterior', 10, 3);
            $table->decimal('stock_nuevo', 10, 3);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['ingrediente_id', 'bodega_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mov_inv_det');
    }
};
