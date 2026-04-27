<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovInv extends Model
{
    use SoftDeletes;

    protected $table = 'mov_inv';

    protected $fillable = [
        'annomes', 'fechahora', 'descripcion', 'observacion', 'staanul',
        'tipo_id', 'modulo_id', 'idmovmod',
        'sucursal_id', 'usuario_id', 'usuarioanul_id',
    ];

    protected $casts = [
        'fechahora' => 'datetime',
        'staanul'   => 'datetime',
    ];

    public function tipo()
    {
        return $this->belongsTo(MovInvTipo::class, 'tipo_id');
    }

    public function modulo()
    {
        return $this->belongsTo(MovInvModulo::class, 'modulo_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function detalles()
    {
        return $this->hasMany(MovInvDet::class, 'mov_inv_id');
    }

    public function scopeActivos($query)
    {
        return $query->whereNull('staanul');
    }

    public function scopePeriodo($query, string $annomes)
    {
        return $query->where('annomes', $annomes);
    }

    public function estaAnulado(): bool
    {
        return $this->staanul !== null;
    }

    /**
     * Anula el movimiento y revierte el stock de cada línea
     */
    public function anular(int $usuarioId): void
    {
        if ($this->estaAnulado()) {
            throw new \RuntimeException('El movimiento ya está anulado.');
        }

        \DB::transaction(function () use ($usuarioId) {
            foreach ($this->detalles as $det) {
                $det->revertirStock();
            }
            $this->update([
                'staanul'       => now(),
                'usuarioanul_id'=> $usuarioId,
            ]);
        });
    }
}
