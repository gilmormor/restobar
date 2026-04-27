<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pais_id')->constrained('paises')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->string('ordinal', 10)->nullable(); // RM, I, II / ANT, BOG
            $table->integer('orden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regiones');
    }
};
