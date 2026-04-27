<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    protected $table = 'comunas';

    protected $fillable = ['provincia_id', 'nombre'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
