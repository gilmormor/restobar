<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Moneda;
use Illuminate\Http\Request;

class MonedaController extends Controller
{
    public function index()
    {
        return response()->json(Moneda::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'simbolo'     => 'required|string|max:10',
            'codigo'      => 'required|string|max:10|unique:monedas,codigo',
            'valor'       => 'required|numeric|min:0',
            'es_local'    => 'boolean',
            'activa'      => 'boolean',
        ]);
        $data['usuario_id'] = auth()->id();
        $moneda = Moneda::create($data);
        return response()->json($moneda, 201);
    }

    public function show(Moneda $moneda)
    {
        return response()->json($moneda);
    }

    public function update(Request $request, Moneda $moneda)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'simbolo'     => 'sometimes|string|max:10',
            'codigo'      => 'sometimes|string|max:10|unique:monedas,codigo,'.$moneda->id,
            'valor'       => 'sometimes|numeric|min:0',
            'es_local'    => 'boolean',
            'activa'      => 'boolean',
        ]);
        $moneda->update($data);
        return response()->json($moneda);
    }

    public function destroy(Moneda $moneda)
    {
        if ($moneda->empresas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la moneda está en uso por empresas.',
            ], 422);
        }
        $moneda->delete();
        return response()->json(['message' => 'Moneda eliminada']);
    }
}
