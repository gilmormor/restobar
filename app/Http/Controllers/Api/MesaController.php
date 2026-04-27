<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MesaController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(Mesa::with(['ambiente', 'pedidoActivo'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ambiente_id' => 'required|exists:ambientes,id',
            'numero'      => 'required|integer',
            'capacidad'   => 'integer|min:1',
            'pos_x'       => 'numeric',
            'pos_y'       => 'numeric',
        ]);
        return response()->json(Mesa::create($data), 201);
    }

    public function show(Mesa $mesa)
    {
        return response()->json($mesa->load(['ambiente', 'pedidoActivo.items.plato']));
    }

    public function update(Request $request, Mesa $mesa)
    {
        // El frontend tomó updated_at al hacer clic en "Editar"
        if ($lock = $this->checkLock($request, $mesa)) return $lock;

        $mesa->update($request->validate([
            'ambiente_id' => 'sometimes|exists:ambientes,id',
            'numero'      => 'sometimes|integer',
            'capacidad'   => 'integer|min:1',
            'pos_x'       => 'numeric',
            'pos_y'       => 'numeric',
            'activa'      => 'boolean',
        ]));

        return response()->json($mesa);
    }

    public function destroy(Request $request, Mesa $mesa)
    {
        // El frontend tomó updated_at al hacer clic en "Eliminar"
        if ($lock = $this->checkLock($request, $mesa)) return $lock;

        // Verificar que no tenga registros hijos
        if ($mesa->pedidos()->exists()) {
            $cantidad = $mesa->pedidos()->count();
            return response()->json([
                'error'   => 'has_relations',
                'message' => "No se puede eliminar: la mesa tiene {$cantidad} pedido(s) registrado(s). Primero cierra o elimina los pedidos asociados.",
            ], 422);
        }

        try {
            $mesa->delete();
            return response()->json(['message' => 'Mesa eliminada correctamente']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error'   => 'has_relations',
                    'message' => 'No se puede eliminar: la mesa tiene registros asociados en el sistema.',
                ], 422);
            }
            throw $e;
        }
    }

    public function cambiarEstado(Request $request, Mesa $mesa)
    {
        $request->validate(['estado' => 'required|in:libre,ocupada,reservada,cerrada']);

        // El frontend tomó updated_at al hacer clic en el botón de estado
        if ($lock = $this->checkLock($request, $mesa)) return $lock;

        $mesa->update(['estado' => $request->estado]);
        return response()->json($mesa);
    }

    public function actualizarPosicion(Request $request, Mesa $mesa)
    {
        // Drag & drop: no aplicamos bloqueo optimista (solo mueve posición visual)
        $request->validate(['pos_x' => 'required|numeric', 'pos_y' => 'required|numeric']);
        $mesa->update(['pos_x' => $request->pos_x, 'pos_y' => $request->pos_y]);
        return response()->json($mesa);
    }
}
