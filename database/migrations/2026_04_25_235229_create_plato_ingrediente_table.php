<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plato_ingrediente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plato_id')->constrained('platos')->onDelete('cascade');
            $table->foreignId('ingrediente_id')->constrained('ingredientes')->onDelete('cascade');
            $table->decimal('cantidad', 10, 3); // cantidad en la unidad del ingrediente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plato_ingrediente');
    }
};
