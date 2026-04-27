<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisesYMonedasSeeder extends Seeder
{
    public function run(): void
    {
        // ── Países ────────────────────────────────────────────────────────────
        $paises = [
            ['nombre' => 'Chile',    'codigo_iso2' => 'CL', 'codigo_iso3' => 'CHL', 'tipo_id_fiscal' => 'RUT', 'moneda_codigo' => 'CLP'],
            ['nombre' => 'Colombia', 'codigo_iso2' => 'CO', 'codigo_iso3' => 'COL', 'tipo_id_fiscal' => 'NIT', 'moneda_codigo' => 'COP'],
            ['nombre' => 'Venezuela','codigo_iso2' => 'VE', 'codigo_iso3' => 'VEN', 'tipo_id_fiscal' => 'RIF', 'moneda_codigo' => 'VES'],
            ['nombre' => 'Perú',     'codigo_iso2' => 'PE', 'codigo_iso3' => 'PER', 'tipo_id_fiscal' => 'RUC', 'moneda_codigo' => 'PEN'],
            ['nombre' => 'México',   'codigo_iso2' => 'MX', 'codigo_iso3' => 'MEX', 'tipo_id_fiscal' => 'RFC', 'moneda_codigo' => 'MXN'],
        ];

        foreach ($paises as $p) {
            DB::table('paises')->insertOrIgnore(array_merge($p, [
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Monedas ───────────────────────────────────────────────────────────
        $monedas = [
            ['nombre' => 'Peso Chileno',     'codigo' => 'CLP', 'simbolo' => '$',   'valor' => 1,       'es_local' => true],
            ['nombre' => 'Peso Colombiano',  'codigo' => 'COP', 'simbolo' => '$',   'valor' => 0.00027, 'es_local' => false],
            ['nombre' => 'Bolívar Soberano', 'codigo' => 'VES', 'simbolo' => 'Bs.', 'valor' => 0.000030,'es_local' => false],
            ['nombre' => 'Sol Peruano',      'codigo' => 'PEN', 'simbolo' => 'S/',  'valor' => 0.0037,  'es_local' => false],
            ['nombre' => 'Peso Mexicano',    'codigo' => 'MXN', 'simbolo' => '$',   'valor' => 0.053,   'es_local' => false],
            ['nombre' => 'Dólar Americano',  'codigo' => 'USD', 'simbolo' => 'US$', 'valor' => 0.00108, 'es_local' => false],
            ['nombre' => 'Euro',             'codigo' => 'EUR', 'simbolo' => '€',   'valor' => 0.00099, 'es_local' => false],
        ];

        foreach ($monedas as $m) {
            DB::table('monedas')->insertOrIgnore(array_merge($m, [
                'activa'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
