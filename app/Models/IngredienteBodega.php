<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredienteBodega extends Model
{
    protected $table = 'ingrediente_bodega';

    protected $fillable = [
        'ingrediente_id', 'bodega_id',
        'stock_actual', 'stock_minimo', 'alerta_minimo'
    ];

    protected $casts = [
        'stock_actual'  => 'decimal:3',
        'stock_minimo'  => 'decimal:3',
        'alerta_minimo' => 'boolean',
    ];

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function esBajoMinimo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
