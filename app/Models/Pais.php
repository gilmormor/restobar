<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    protected $fillable = [
        'nombre', 'codigo_iso2', 'codigo_iso3',
        'tipo_id_fiscal', 'moneda_codigo', 'activo',
    ];

    public function regiones()
    {
        return $this->hasMany(Region::class, 'pais_id');
    }
}
