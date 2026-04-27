<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'sucursal_id', 'usuario_id', 'monto_inicial', 'monto_final',
        'total_ventas', 'total_domicilios', 'estado',
        'fecha_apertura', 'fecha_cierre', 'observaciones'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
