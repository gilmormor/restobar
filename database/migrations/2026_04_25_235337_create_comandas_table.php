<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('impresora_id')->nullable()->constrained('impresoras')->nullOnDelete();
            $table->json('items'); // snapshot de los items enviados (sin precios)
            $table->boolean('impresa')->default(false);
            $table->timestamp('impresa_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
