<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingrediente;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class IngredienteController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(
            Ingrediente::with('bodegas')
                ->orderBy('nombre')
                ->get()
                ->map(function ($ing) {
                    $ing->stock_total = $ing->bodegas->sum('pivot.stock_actual');
                    return $ing;
                })
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:150',
            'unidad_stock'  => 'required|string|max:20',
            'costo_unitario'=> 'numeric|min:0',
        ]);
        return response()->json(Ingrediente::create($data), 201);
    }

    public function show(Ingrediente $ingrediente)
    {
        return response()->json(
            $ingrediente->load(['bodegas', 'movimientos' => fn($q) => $q->latest()->limit(50)])
        );
    }

    public function update(Request $request, Ingrediente $ingrediente)
    {
        if ($lock = $this->checkLock($request, $ingrediente)) return $lock;

        $ingrediente->update($request->validate([
            'nombre'        => 'sometimes|string|max:150',
            'unidad_stock'  => 'sometimes|string|max:20',
            'costo_unitario'=> 'numeric|min:0',
            'activo'        => 'boolean',
        ]));
        return response()->json($ingrediente);
    }

    public function destroy(Request $request, Ingrediente $ingrediente)
    {
        if ($lock = $this->checkLock($request, $ingrediente)) return $lock;

        if ($ingrediente->platos()->exists()) {
            $count = $ingrediente->platos()->count();
            return response()->json([
                'error'   => 'has_relations',
                'message' => "No se puede eliminar: el ingrediente está en {$count} receta(s).",
            ], 422);
        }

        try {
            $ingrediente->delete();
            return response()->json(['message' => 'Ingrediente eliminado correctamente']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error'   => 'has_relations',
                    'message' => 'No se puede eliminar: el ingrediente tiene registros asociados.',
                ], 422);
            }
            throw $e;
        }
    }
}
