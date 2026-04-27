<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $fillable = ['nombre', 'unidad_stock', 'costo_unitario', 'activo'];

    protected $casts = ['costo_unitario' => 'decimal:4', 'activo' => 'boolean'];

    public function platos()
    {
        return $this->belongsToMany(Plato::class, 'plato_ingrediente')
            ->withPivot('cantidad', 'unidad_uso', 'factor_conversion');
    }

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'ingrediente_bodega')
            ->withPivot('stock_actual', 'stock_minimo', 'alerta_minimo');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Total stock across all warehouses
     */
    public function getStockTotalAttribute(): float
    {
        return (float) $this->bodegas->sum('pivot.stock_actual');
    }
}
