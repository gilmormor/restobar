<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cabecera del movimiento de inventario
        // Un movimiento puede tener múltiples líneas (mov_inv_det)
        Schema::create('mov_inv', function (Blueprint $table) {
            $table->id();
            // Período contable AAAAMM (ej: 202604) — sin guión, más compacto e indexable
            $table->char('annomes', 6)->index()->comment('Período AAAAMM, ej: 202604');
            $table->dateTime('fechahora');
            $table->string('descripcion', 300)->nullable();
            $table->string('observacion', 300)->nullable();
            // Anulación: guarda la fecha en que fue anulado (NULL = activo)
            // No se borra el registro, se anula para mantener trazabilidad
            $table->dateTime('staanul')->nullable()->comment('Fecha anulación, NULL=activo');

            $table->foreignId('tipo_id')->constrained('mov_inv_tipos')->onDelete('restrict');
            $table->foreignId('modulo_id')->constrained('mov_inv_modulos')->onDelete('restrict');
            // ID del registro origen en el módulo (ej: pedido_id, compra_id)
            $table->unsignedBigInteger('idmovmod')->nullable()->comment('ID origen en el módulo');

            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('restrict');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('usuarioanul_id')->nullable()->constrained('usuarios')->nullOnDelete()
                ->comment('Usuario que anuló el movimiento');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['annomes', 'tipo_id']);
            $table->index(['annomes', 'sucursal_id']);
            $table->index(['modulo_id', 'idmovmod']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mov_inv');
    }
};
