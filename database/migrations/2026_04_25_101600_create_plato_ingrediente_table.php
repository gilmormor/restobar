<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recipe: which ingredients a plate uses, with dual-unit support
        Schema::create('plato_ingrediente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plato_id')->constrained('platos')->onDelete('cascade');
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->onDelete('restrict');
            // Amount consumed per plate, expressed in unidad_uso
            $table->decimal('cantidad', 10, 3);
            // Unit used in recipe (may differ from unidad_stock)
            // E.g. ingrediente.unidad_stock='kg', unidad_uso='g' → factor_conversion=0.001
            $table->string('unidad_uso');
            // How many unidad_stock equals one unidad_uso
            // E.g. 1g = 0.001kg → factor_conversion = 0.001
            $table->decimal('factor_conversion', 12, 6)->default(1.000000);
            $table->timestamps();

            $table->unique(['plato_id', 'ingrediente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plato_ingrediente');
    }
};
