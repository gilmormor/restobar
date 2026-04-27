<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monedas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);           // Peso Chileno
            $table->string('descripcion')->nullable();
            $table->string('simbolo', 10);           // $, COP, Bs
            $table->string('codigo', 10)->unique();  // CLP, COP, VES, USD
            $table->decimal('valor', 15, 4)->default(1); // Valor vs. moneda local
            $table->boolean('es_local')->default(false);
            $table->boolean('activa')->default(true);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monedas');
    }
};
