<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SucursalController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(
            Sucursal::with(['region', 'provincia', 'comuna'])
                ->withCount(['ambientes', 'pedidos'])
                ->orderBy('nombre')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'abrev'       => 'nullable|string|max:20',
            'direccion'   => 'nullable|string',
            'telefono'    => 'nullable|string|max:30',
            'telefonos'   => 'nullable|string',
            'email'       => 'nullable|email',
            'logo'        => 'nullable|string',
            'activa'      => 'boolean',
            'region_id'   => 'nullable|exists:regiones,id',
            'provincia_id'=> 'nullable|exists:provincias,id',
            'comuna_id'   => 'nullable|exists:comunas,id',
        ]);
        $sucursal = Sucursal::create($data);
        return response()->json(
            $sucursal->load(['region', 'provincia', 'comuna']),
            201
        );
    }

    public function show(Sucursal $sucursal)
    {
        return response()->json($sucursal->load(['bodegas', 'ambientes.mesas', 'usuarios']));
    }

    public function update(Request $request, int $id)
    {
        $sucursal = Sucursal::findOrFail($id);

        if ($lock = $this->checkLock($request, $sucursal)) return $lock;

        $sucursal->update($request->validate([
            'nombre'      => 'sometimes|string|max:150',
            'abrev'       => 'nullable|string|max:20',
            'direccion'   => 'nullable|string',
            'telefono'    => 'nullable|string|max:30',
            'telefonos'   => 'nullable|string',
            'email'       => 'nullable|email',
            'logo'        => 'nullable|string',
            'activa'      => 'boolean',
            'region_id'   => 'nullable|exists:regiones,id',
            'provincia_id'=> 'nullable|exists:provincias,id',
            'comuna_id'   => 'nullable|exists:comunas,id',
        ]));

        return response()->json(
            $sucursal->load(['region', 'provincia', 'comuna'])
        );
    }

    public function destroy(Request $request, int $id)
    {
        $sucursal = Sucursal::findOrFail($id);

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
