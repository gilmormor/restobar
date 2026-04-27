<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $fillable = ['sucursal_id', 'nombre', 'ip', 'puerto', 'tipo', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
