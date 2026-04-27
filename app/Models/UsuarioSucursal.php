<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioSucursal extends Model
{
    protected $table = 'usuario_sucursal';

    protected $fillable = ['usuario_id', 'sucursal_id', 'rol_id', 'es_principal', 'activo'];

    protected $casts = ['es_principal' => 'boolean', 'activo' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }
}
