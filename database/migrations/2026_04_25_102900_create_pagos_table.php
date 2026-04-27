<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A factura can be paid with multiple payment methods (split payment)
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->enum('metodo', ['efectivo', 'tarjeta', 'transferencia', 'qr', 'credito'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable(); // card auth number, transfer ref, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
