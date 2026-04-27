<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'monedas';

    protected $fillable = [
        'nombre', 'descripcion', 'simbolo', 'codigo',
        'valor', 'es_local', 'activa', 'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'moneda_id');
    }
}
