<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'bodega_sucursal')
            ->withPivot('es_principal');
    }

    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_bodega')
            ->withPivot('stock_actual', 'stock_minimo', 'alerta_minimo');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
