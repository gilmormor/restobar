<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    protected $fillable = ['pedido_id', 'impresora_id', 'items', 'impresa', 'impresa_en'];
    protected $casts = ['items' => 'array'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function impresora()
    {
        return $this->belongsTo(Impresora::class);
    }
}
