<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'usuario_id', 'nombre', 'apellido', 'cedula',
        'telefono', 'cargo', 'fecha_ingreso', 'activo'
    ];

    protected $casts = ['fecha_ingreso' => 'date', 'activo' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function ambientes()
    {
        return $this->belongsToMany(Ambiente::class, 'mesonero_ambiente');
    }
}
