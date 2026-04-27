<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovInvModulo extends Model
{
    protected $table = 'mov_inv_modulos';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function movimientos()
    {
        return $this->hasMany(MovInv::class, 'modulo_id');
    }

    // IDs fijos de módulos (se pueden usar como constantes)
    public const MANUAL       = 1;
    public const VENTA        = 2;
    public const COMPRA       = 3;
    public const AJUSTE       = 4;
    public const TRANSFERENCIA= 5;
    public const CIERRE_MES   = 6;
}
