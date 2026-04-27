<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre', 'abrev', 'direccion', 'telefono', 'telefonos',
        'email', 'logo', 'activa',
        'empresa_id', 'region_id', 'provincia_id', 'comuna_id',
        'usuario_id', 'usuariodel_id',
    ];

    protected $casts = ['activa' => 'boolean'];

    public function empresa()   { return $this->belongsTo(Empresa::class,  'empresa_id'); }
    public function region()    { return $this->belongsTo(Region::class,   'region_id'); }
    public function provincia() { return $this->belongsTo(Provincia::class,'provincia_id'); }
    public function comuna()    { return $this->belongsTo(Comuna::class,   'comuna_id'); }

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
