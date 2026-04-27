<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use App\Models\Region;
use App\Models\Provincia;
use App\Models\Comuna;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function paises()
    {
        return response()->json(Pais::where('activo', true)->orderBy('nombre')->get());
    }

    public function regiones(Request $request)
    {
        $query = Region::orderBy('orden')->orderBy('nombre');
        if ($request->filled('pais_id')) {
            $query->where('pais_id', $request->pais_id);
        }
        return response()->json($query->get());
    }

    public function provincias(Request $request)
    {
        $query = Provincia::orderBy('nombre');
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        return response()->json($query->get());
    }

    public function comunas(Request $request)
    {
        $query = Comuna::orderBy('nombre');
        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }
        return response()->json($query->get());
    }
}
