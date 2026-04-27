<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mov_inv_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 300)->nullable();
            // Multiplicador: 1 = suma al stock (entrada), -1 = resta al stock (salida)
            // Permite calcular stock con un simple SUM(cantidad * tipomov)
            $table->tinyInteger('tipomov')->default(1)->comment('1=entrada, -1=salida');
            // Indica que este tipo crea el saldo inicial de un período (apertura de mes)
            $table->boolean('es_apertura_mes')->default(false);
            $table->boolean('activo')->default(true);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mov_inv_tipos');
    }
};
