<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User-branch-role assignment: a user can work at multiple branches with different roles
        Schema::create('usuario_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('restrict');
            $table->boolean('es_principal')->default(false); // default branch on login
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['usuario_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_sucursal');
    }
};
