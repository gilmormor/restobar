<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            // How we BUY and STORE: 'kg', 'l', 'und', 'g', 'ml', 'lb', 'oz', etc.
            // Free string so the business can define any unit
            $table->string('unidad_stock');
            // Reference cost per unit of unidad_stock (for valuation & reports)
            $table->decimal('costo_unitario', 10, 4)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredientes');
    }
};
