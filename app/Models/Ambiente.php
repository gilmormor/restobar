<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    protected $fillable = ['sucursal_id', 'nombre', 'descripcion', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }

    public function mesoneros()
    {
        return $this->belongsToMany(Empleado::class, 'mesonero_ambiente');
    }
}
