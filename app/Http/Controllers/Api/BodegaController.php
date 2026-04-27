<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bodega;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(Bodega::with('sucursales')->orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'activa'      => 'boolean',
            // Optional: link to a sucursal on creation
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $sucursalId = $data['sucursal_id'] ?? null;
        unset($data['sucursal_id']);

        $bodega = Bodega::create($data);

        if ($sucursalId) {
            $bodega->sucursales()->attach($sucursalId, ['es_principal' => true]);
        }

        return response()->json($bodega->load('sucursales'), 201);
    }

    public function show(Bodega $bodega)
    {
        return response()->json($bodega->load(['sucursales', 'ingredientes']));
    }

    public function update(Request $request, Bodega $bodega)
    {
        if ($lock = $this->checkLock($request, $bodega)) return $lock;

        $bodega->update($request->validate([
            'nombre'      => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'activa'      => 'boolean',
        ]));
        return response()->json($bodega->load('sucursales'));
    }

    public function destroy(Request $request, Bodega $bodega)
    {
        if ($lock = $this->checkLock($request, $bodega)) return $lock;

        if ($bodega->movimientos()->exists()) {
            return response()->json([
                'error'   => 'has_relations',
                'message' => 'No se puede eliminar: la bodega tiene movimientos de inventario.',
            ], 422);
        }

        $bodega->delete();
        return response()->json(['message' => 'Bodega eliminada correctamente']);
    }
}
