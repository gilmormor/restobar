<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion', 'precio',
        'foto', 'disponible', 'motivo_no_disponible', 'activo'
    ];

    protected $casts = [
        'precio'     => 'decimal:2',
        'disponible' => 'boolean',
        'activo'     => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'plato_ingrediente')
            ->withPivot('cantidad', 'unidad_uso', 'factor_conversion');
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'plato_sucursal')
            ->withPivot('precio', 'disponible', 'motivo_no_disponible', 'activo');
    }

    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function promociones()
    {
        return $this->hasMany(Promocion::class);
    }

    /**
     * Get the effective price for a given branch (null = use global price)
     */
    public function precioEnSucursal(int $sucursalId): float
    {
        $pivot = $this->sucursales()->where('sucursal_id', $sucursalId)->first();
        return (float) ($pivot?->pivot->precio ?? $this->precio);
    }
}
