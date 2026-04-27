<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = ['nombre', 'descripcion', 'es_superadmin', 'activo'];

    protected $casts = ['es_superadmin' => 'boolean', 'activo' => 'boolean'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_rol');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'permiso_rol');
    }

    public function usuarioSucursales()
    {
        return $this->hasMany(UsuarioSucursal::class);
    }
}
