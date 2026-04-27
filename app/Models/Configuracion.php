<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = ['sucursal_id', 'clave', 'valor', 'descripcion'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Get a config value by key (global or branch-specific)
     */
    public static function get(string $clave, ?int $sucursalId = null, mixed $default = null): mixed
    {
        $query = static::where('clave', $clave);

        if ($sucursalId) {
            // Branch-specific value takes precedence over global
            $branchVal = (clone $query)->where('sucursal_id', $sucursalId)->value('valor');
            if ($branchVal !== null) return $branchVal;
        }

        return $query->whereNull('sucursal_id')->value('valor') ?? $default;
    }
}
