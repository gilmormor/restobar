<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Registro de apertura y cierre de períodos de inventario por sucursal.
        // Antes de registrar movimientos de un mes, ese mes debe estar aperturado (status=0).
        // Al ejecutar el cierre, el mes pasa a status=1 y se crea el siguiente en status=0.
        Schema::create('inv_control', function (Blueprint $table) {
            $table->id();
            $table->char('annomes', 6)->comment('Período AAAAMM, ej: 202604');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            // 0 = abierto (se pueden registrar movimientos)
            // 1 = cerrado (período congelado, no se admiten nuevos movimientos)
            $table->tinyInteger('status')->default(0)->comment('0=abierto, 1=cerrado');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('usuariodel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['annomes', 'sucursal_id']);
            $table->index(['sucursal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_control');
    }
};
