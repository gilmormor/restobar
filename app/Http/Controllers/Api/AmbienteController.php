<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ambiente;
use Illuminate\Http\Request;

class AmbienteController extends Controller
{
    public function index(Request $request)
    {
        $q = Ambiente::with('mesas');

        if ($request->filtraSucursal()) {
            $q->where('sucursal_id', $request->sucursalId());
        }

        return response()->json($q->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $data['sucursal_id'] = $request->sucursalId();

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
