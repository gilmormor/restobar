<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CategoriaController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(
            Categoria::orderBy('orden')
                ->withCount('platos')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'icono'       => 'nullable|string|max:10',
            'orden'       => 'integer',
        ]);
        return response()->json(Categoria::create($data), 201);
    }

    public function show(Categoria $categoria)
    {
        return response()->json($categoria->load('platos'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        if ($lock = $this->checkLock($request, $categoria)) return $lock;

        $categoria->update($request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'icono'       => 'nullable|string|max:10',
            'orden'       => 'integer',
            'activa'      => 'boolean',
        ]));
        return response()->json($categoria);
    }

    public function destroy(Request $request, Categoria $categoria)
    {
        if ($lock = $this->checkLock($request, $categoria)) return $lock;

        if ($categoria->platos()->exists()) {
            $cantidad = $categoria->platos()->count();
            return response()->json([
                'error'   => 'has_relations',
                'message' => "No se puede eliminar: la categoría tiene {$cantidad} plato(s) asociado(s). Primero elimina o mueve los platos.",
            ], 422);
        }

        try {
            $categoria->delete();
            return response()->json(['message' => 'Categoría eliminada correctamente']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error'   => 'has_relations',
                    'message' => 'No se puede eliminar: la categoría tiene registros asociados.',
                ], 422);
            }
            throw $e;
        }
    }
}
