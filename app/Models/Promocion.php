<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'plato_id', 'sucursal_id', 'nombre', 'descripcion',
        'descuento_porcentaje', 'precio_promocional',
        'fecha_inicio', 'fecha_fin', 'activa'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activa'       => 'boolean',
    ];

    public function plato()
    {
        return $this->belongsTo(Plato::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
