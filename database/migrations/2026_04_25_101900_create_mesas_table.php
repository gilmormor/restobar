<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambiente_id')->constrained('ambientes')->onDelete('cascade');
            $table->integer('numero');
            $table->integer('capacidad')->default(4);
            $table->decimal('pos_x', 8, 2)->default(0); // drag & drop position
            $table->decimal('pos_y', 8, 2)->default(0);
            $table->enum('estado', ['libre', 'ocupada', 'reservada', 'cerrada'])->default('libre');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
