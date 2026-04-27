<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MovInvDet extends Model
{
    use SoftDeletes;

    protected $table = 'mov_inv_det';

    protected $fillable = [
        'mov_inv_id', 'ingrediente_id', 'bodega_id', 'tipo_id',
        'cantidad', 'unidad_uso', 'factor_conv', 'cantidad_stock',
        'costo_unitario', 'stock_anterior', 'stock_nuevo',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:3',
        'factor_conv'    => 'decimal:6',
        'cantidad_stock' => 'decimal:3',
        'costo_unitario' => 'decimal:4',
        'stock_anterior' => 'decimal:3',
        'stock_nuevo'    => 'decimal:3',
    ];

    public function movimiento()
    {
        return $this->belongsTo(MovInv::class, 'mov_inv_id');
    }

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function tipo()
    {
        return $this->belongsTo(MovInvTipo::class, 'tipo_id');
    }

    /**
     * Revierte el efecto de esta línea en el stock.
     * Se usa al anular un movimiento.
     * Si el tipo sumó (+1), la reversión resta. Si restó (-1), la reversión suma.
     */
    public function revertirStock(): void
    {
        $tipomov = $this->tipo->tipomov; // 1 ó -1

        DB::table('ingrediente_bodega')
            ->where('ingrediente_id', $this->ingrediente_id)
            ->where('bodega_id', $this->bodega_id)
            // La reversión es el efecto contrario: si sumó, ahora restamos
            ->decrement('stock_actual', $this->cantidad_stock * $tipomov);
    }
}
