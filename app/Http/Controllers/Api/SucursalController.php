<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(Sucursal::withCount(['ambientes', 'pedidos'])->orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:150',
            'direccion' => 'nullable|string',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email',
            'logo'      => 'nullable|string',
            'activa'    => 'boolean',
        ]);
        return response()->json(Sucursal::create($data), 201);
    }

    public function show(Sucursal $sucursal)
    {
        return response()->json($sucursal->load(['bodegas', 'ambientes.mesas', 'usuarios']));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        if ($lock = $this->checkLock($request, $sucursal)) return $lock;

        $sucursal->update($request->validate([
            'nombre'    => 'sometimes|string|max:150',
            'direccion' => 'nullable|string',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email',
            'logo'      => 'nullable|string',
            'activa'    => 'boolean',
        ]));
        return response()->json($sucursal);
    }

    public function destroy(Request $request, Sucursal $sucursal)
    {
        if ($lock = $this->checkLock($request, $sucursal)) return $lock;

        if ($sucursal->pedidos()->exists()) {
            return response()->json([
                'error'   => 'has_relations',
                'message' => 'No se puede eliminar: la sucursal tiene pedidos registrados.',
            ], 422);
        }

        $sucursal->delete();
        return response()->json(['message' => 'Sucursal eliminada correctamente']);
    }
}
