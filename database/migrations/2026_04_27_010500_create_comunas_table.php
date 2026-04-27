<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provincia_id')->constrained('provincias')->onDelete('cascade');
            $table->string('nombre', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunas');
    }
};
