<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    protected $fillable = [
        'pedido_id', 'repartidor_id', 'cliente_nombre', 'cliente_telefono',
        'direccion', 'referencia', 'estado', 'entregado_en'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function repartidor()
    {
        return $this->belongsTo(Empleado::class, 'repartidor_id');
    }
}
