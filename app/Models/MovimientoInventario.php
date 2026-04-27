<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    // Immutable: never allow mass-update of movements
    protected $fillable = [
        'ingrediente_id', 'bodega_id', 'usuario_id', 'tipo',
        'cantidad', 'costo_unitario', 'stock_anterior', 'stock_nuevo',
        'referencia_tipo', 'referencia_id', 'notas',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:3',
        'costo_unitario' => 'decimal:4',
        'stock_anterior' => 'decimal:3',
        'stock_nuevo'    => 'decimal:3',
    ];

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
