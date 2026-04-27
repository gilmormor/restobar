<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre', 'nombre_comercial', 'tipo_id_fiscal', 'id_fiscal',
        'giro', 'direccion', 'telefono', 'email', 'logo',
        'moneda_id', 'pais_id', 'activa',
    ];

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'empresa_id');
    }
}
