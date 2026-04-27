<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = ['nombre', 'direccion', 'telefono', 'email', 'logo', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'bodega_sucursal')
            ->withPivot('es_principal');
    }

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_sucursal')
            ->withPivot('rol_id', 'es_principal', 'activo');
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function impresoras()
    {
        return $this->hasMany(Impresora::class);
    }

    public function configuraciones()
    {
        return $this->hasMany(Configuracion::class);
    }
}
