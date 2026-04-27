<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plato;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PlatoController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(Plato::with('categoria')->orderBy('categoria_id')->orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id'         => 'required|exists:categorias,id',
            'nombre'               => 'required|string|max:150',
            'descripcion'          => 'nullable|string',
            'precio'               => 'required|numeric|min:0',
            'foto'                 => 'nullable|string',
            'disponible'           => 'boolean',
            'motivo_no_disponible' => 'nullable|string',
        ]);
        $plato = Plato::create($data);
        return response()->json($plato->load('categoria'), 201);
    }

    public function show(Plato $plato)
    {
        return response()->json($plato->load(['categoria', 'ingredientes']));
    }

    public function update(Request $request, Plato $plato)
    {
        if ($lock = $this->checkLock($request, $plato)) return $lock;

        $plato->update($request->validate([
            'categoria_id'         => 'sometimes|exists:categorias,id',
            'nombre'               => 'sometimes|string|max:150',
            'descripcion'          => 'nullable|string',
            'precio'               => 'numeric|min:0',
            'foto'                 => 'nullable|string',
            'disponible'           => 'boolean',
            'motivo_no_disponible' => 'nullable|string',
            'activo'               => 'boolean',
        ]));
        return response()->json($plato->load('categoria'));
    }

    public function destroy(Request $request, Plato $plato)
    {
        if ($lock = $this->checkLock($request, $plato)) return $lock;

        // Verificar si el plato tiene items en pedidos activos
        $enPedidosActivos = $plato->pedidoItems()
            ->whereHas('pedido', fn($q) => $q->where('estado', 'abierto'))
            ->exists();

        if ($enPedidosActivos) {
            return response()->json([
                'error'   => 'has_relations',
                'message' => 'No se puede eliminar: el plato tiene pedidos activos en curso.',
            ], 422);
        }

        try {
            $plato->delete();
            return response()->json(['message' => 'Plato eliminado correctamente']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error'   => 'has_relations',
                    'message' => 'No se puede eliminar: el plato tiene registros asociados en el sistema.',
                ], 422);
            }
            throw $e;
        }
    }

    public function cambiarDisponibilidad(Request $request, Plato $plato)
    {
        if ($lock = $this->checkLock($request, $plato)) return $lock;

        $request->validate([
            'disponible'           => 'required|boolean',
            'motivo_no_disponible' => 'nullable|string|max:200',
        ]);

        $plato->update([
            'disponible'           => $request->disponible,
            'motivo_no_disponible' => $request->disponible ? null : $request->motivo_no_disponible,
        ]);
        return response()->json($plato);
    }
}
