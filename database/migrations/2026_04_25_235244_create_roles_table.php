<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // admin, subadmin, cajero, mesonero
            $table->json('permisos')->nullable(); // array de permisos
            $table->boolean('ver_totales')->default(false); // si puede ver totales de mesas
            $table->boolean('puede_eliminar')->default(false);
            $table->boolean('puede_descontar')->default(false);
            $table->boolean('requiere_autorizacion')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
