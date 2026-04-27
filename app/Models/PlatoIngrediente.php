<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlatoIngrediente extends Pivot
{
    protected $table = 'plato_ingrediente';

    protected $fillable = ['plato_id', 'ingrediente_id', 'cantidad', 'unidad_uso', 'factor_conversion'];

    protected $casts = [
        'cantidad'          => 'decimal:3',
        'factor_conversion' => 'decimal:6',
    ];

    /**
     * Amount consumed expressed in unidad_stock units (for inventory deduction)
     * Example: 150g × 0.001 factor = 0.15 kg deducted from stock
     */
    public function getCantidadEnStockAttribute(): float
    {
        return (float) ($this->cantidad * $this->factor_conversion);
    }
}
