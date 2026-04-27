<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = ['ambiente_id', 'numero', 'capacidad', 'pos_x', 'pos_y', 'estado', 'activa'];

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function pedidoActivo()
    {
        return $this->hasOne(Pedido::class)->where('estado', 'abierto');
    }
}
