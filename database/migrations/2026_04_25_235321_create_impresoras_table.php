<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impresoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Cocina, Bar, Pizzería, Hamburguesas
            $table->enum('tipo', ['cocina', 'bar', 'caja', 'otro'])->default('cocina');
            $table->string('ip')->nullable();
            $table->integer('puerto')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impresoras');
    }
};
