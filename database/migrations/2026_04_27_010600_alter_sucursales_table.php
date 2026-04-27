<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->string('abrev', 20)->nullable()->after('nombre');
            $table->string('telefonos', 200)->nullable()->after('telefono');
            $table->foreignId('empresa_id')->nullable()->after('activa')
                  ->constrained('empresas')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->after('empresa_id')
                  ->constrained('regiones')->nullOnDelete();
            $table->foreignId('provincia_id')->nullable()->after('region_id')
                  ->constrained('provincias')->nullOnDelete();
            $table->foreignId('comuna_id')->nullable()->after('provincia_id')
                  ->constrained('comunas')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->after('comuna_id')
                  ->constrained('usuarios')->nullOnDelete();
            $table->foreignId('usuariodel_id')->nullable()->after('usuario_id')
                  ->constrained('usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['comuna_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['usuariodel_id']);
            $table->dropColumn(['abrev', 'telefonos', 'empresa_id', 'region_id',
                                 'provincia_id', 'comuna_id', 'usuario_id', 'usuariodel_id']);
        });
    }
};
