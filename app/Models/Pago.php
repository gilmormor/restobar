<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = ['factura_id', 'metodo', 'monto', 'monto_recibido', 'vuelto', 'referencia'];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
