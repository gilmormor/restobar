<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Employee HR records — not all employees have system login access
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            // Link to login account (nullable: employee may not have system access)
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->nullable()->unique();
            $table->string('telefono')->nullable();
            $table->string('cargo')->nullable();       // job title
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
