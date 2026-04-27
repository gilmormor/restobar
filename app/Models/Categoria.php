<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'icono', 'imagen', 'orden', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function platos()
    {
        return $this->hasMany(Plato::class);
    }

    public function platosDisponibles()
    {
        return $this->hasMany(Plato::class)->where('disponible', true)->where('activo', true);
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'categoria_sucursal')
            ->withPivot('orden', 'activa');
    }
}
