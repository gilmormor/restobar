<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $fillable = [
        'pedido_id', 'plato_id', 'impresora_id',
        'cantidad', 'precio_unitario', 'notas', 'estado', 'enviado_cocina'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function plato()
    {
        return $this->belongsTo(Plato::class);
    }

    public function impresora()
    {
        return $this->belongsTo(Impresora::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->precio_unitario * $this->cantidad;
    }
}
