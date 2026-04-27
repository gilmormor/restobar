<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assignment of waiter/mesonero to specific dining areas
        Schema::create('mesonero_ambiente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->foreignId('ambiente_id')->constrained('ambientes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['empleado_id', 'ambiente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesonero_ambiente');
    }
};
