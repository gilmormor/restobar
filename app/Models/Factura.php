<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'pedido_id', 'caja_id', 'numero', 'subtotal',
        'impuesto', 'descuento', 'propina', 'total', 'estado'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
