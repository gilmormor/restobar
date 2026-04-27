<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';

    protected $fillable = ['region_id', 'nombre'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function comunas()
    {
        return $this->hasMany(Comuna::class, 'provincia_id');
    }
}
