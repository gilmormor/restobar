<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ambiente;
use Illuminate\Http\Request;

class AmbienteController extends Controller
{
    public function index()
    {
        return response()->json(Ambiente::with('mesas')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);
        $ambiente = Ambiente::create($data);
        return response()->json($ambiente, 201);
    }

    public function show(Ambiente $ambiente)
    {
        return response()->json($ambiente->load('mesas'));
    }

    public function update(Request $request, Ambiente $ambiente)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);
        $ambiente->update($data);
        return response()->json($ambiente);
    }

    public function destroy(Ambiente $ambiente)
    {
        $ambiente->delete();
        return response()->json(['message' => 'Ambiente eliminado']);
    }
}
