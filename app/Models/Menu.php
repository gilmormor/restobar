<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['menu_id', 'nombre', 'icono', 'ruta', 'slug', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function padre()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function hijos()
    {
        return $this->hasMany(Menu::class, 'menu_id')->orderBy('orden');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'menu_rol');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class);
    }
}
