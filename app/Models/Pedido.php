<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'sucursal_id', 'mesa_id', 'usuario_id', 'caja_id',
        'tipo', 'estado', 'num_personas', 'notas',
        'fecha_apertura', 'fecha_cierre'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function comandas()
    {
        return $this->hasMany(Comanda::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function domicilio()
    {
        return $this->hasOne(Domicilio::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items->sum(
            fn($item) => ($item->precio_unitario - $item->descuento) * $item->cantidad
        );
    }
}
