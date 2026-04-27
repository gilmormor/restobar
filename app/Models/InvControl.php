<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvControl extends Model
{
    use SoftDeletes;

    protected $table = 'inv_control';

    protected $fillable = ['annomes', 'sucursal_id', 'status', 'usuario_id', 'usuariodel_id'];

    protected $casts = ['status' => 'integer'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function estaAbierto(): bool
    {
        return $this->status === 0;
    }

    public function estaCerrado(): bool
    {
        return $this->status === 1;
    }

    /**
     * Verifica que un período esté abierto para una sucursal.
     * Lanza excepción si no existe o ya está cerrado.
     */
    public static function verificarAbierto(string $annomes, int $sucursalId): self
    {
        $control = static::where('annomes', $annomes)
            ->where('sucursal_id', $sucursalId)
            ->first();

        if (!$control) {
            throw new \RuntimeException("El período {$annomes} no ha sido aperturado para esta sucursal.");
        }

        if ($control->estaCerrado()) {
            throw new \RuntimeException("El período {$annomes} ya fue cerrado.");
        }

        return $control;
    }

    /**
     * Período actualmente abierto para una sucursal.
     */
    public static function periodoAbierto(int $sucursalId): ?self
    {
        return static::where('sucursal_id', $sucursalId)
            ->where('status', 0)
            ->orderBy('annomes', 'desc')
            ->first();
    }
}
