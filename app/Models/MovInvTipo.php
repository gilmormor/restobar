<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovInvTipo extends Model
{
    use SoftDeletes;

    protected $table = 'mov_inv_tipos';

    protected $fillable = ['nombre', 'descripcion', 'tipomov', 'es_apertura_mes', 'activo', 'usuario_id'];

    protected $casts = [
        'tipomov'        => 'integer',
        'es_apertura_mes'=> 'boolean',
        'activo'         => 'boolean',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovInv::class, 'tipo_id');
    }

    public function detalles()
    {
        return $this->hasMany(MovInvDet::class, 'tipo_id');
    }

    // Helpers de consulta
    public function scopeEntradas($query)
    {
        return $query->where('tipomov', 1)->where('es_apertura_mes', false);
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipomov', -1);
    }

    public static function saldoInicial(): self
    {
        return static::where('es_apertura_mes', true)->firstOrFail();
    }
}
