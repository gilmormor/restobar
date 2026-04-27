<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Controls which menu items a role can SEE (visibility only)
        Schema::create('menu_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['menu_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_rol');
    }
};
