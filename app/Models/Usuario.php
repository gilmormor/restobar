<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nombre', 'apellido', 'usuario', 'email', 'password', 'foto', 'activo'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['activo' => 'boolean'];

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'usuario_sucursal')
            ->withPivot('rol_id', 'es_principal', 'activo')
            ->withTimestamps();
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Get the role for a specific branch
     */
    public function rolEnSucursal(int $sucursalId): ?Rol
    {
        $pivot = $this->sucursales()->where('sucursal_id', $sucursalId)->first();
        return $pivot ? Rol::find($pivot->pivot->rol_id) : null;
    }

    /**
     * Get the primary branch for this user
     */
    public function sucursalPrincipal(): ?Sucursal
    {
        return $this->sucursales()->wherePivot('es_principal', true)->first();
    }

    /**
     * Check if user has a permission slug in a given branch
     */
    public function can($ability, $arguments = []): bool
    {
        $rol = $this->rolEnSucursal(
            is_int($arguments) ? $arguments : (session('sucursal_id') ?? 0)
        );

        if (!$rol) return false;
        if ($rol->es_superadmin) return true;

        return $rol->permisos()->where('slug', $ability)->exists();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
