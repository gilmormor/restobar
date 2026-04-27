<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kitchen order tickets: groups of pedido_items sent to kitchen at once
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'en_preparacion', 'lista'])->default('pendiente');
            $table->timestamp('enviada_at')->nullable();
            $table->timestamp('lista_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
