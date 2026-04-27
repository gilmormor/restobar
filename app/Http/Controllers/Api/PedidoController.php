<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Mesa;
use App\Models\Plato;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $q = Pedido::with(['mesa', 'usuario', 'items.plato'])
            ->orderByDesc('created_at');

        if ($request->filtraSucursal()) {
            $q->where('sucursal_id', $request->sucursalId());
        }

        return response()->json($q->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mesa_id'     => 'nullable|exists:mesas,id',
            'usuario_id'  => 'required|exists:usuarios,id',
            'tipo'        => 'in:mesa,domicilio,para_llevar',
            'num_personas'=> 'integer|min:1',
            'notas'       => 'nullable|string',
        ]);

        $data['sucursal_id'] = $request->sucursalId();

        $pedido = Pedido::create($data);

        if ($pedido->mesa_id) {
            Mesa::find($pedido->mesa_id)->update(['estado' => 'ocupada']);
        }

        return response()->json($pedido->load(['mesa', 'usuario']), 201);
    }

    public function show(Pedido $pedido)
    {
        return response()->json($pedido->load(['mesa', 'usuario', 'items.plato', 'domicilio']));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $pedido->update($request->validate([
            'notas'        => 'nullable|string',
            'num_personas' => 'integer|min:1',
        ]));
        return response()->json($pedido);
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response()->json(['message' => 'Pedido eliminado']);
    }

    public function items(Pedido $pedido)
    {
        return response()->json($pedido->items()->with('plato')->get());
    }

    public function agregarItem(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'plato_id'    => 'required|exists:platos,id',
            'cantidad'    => 'required|integer|min:1',
            'notas'       => 'nullable|string',
            'impresora_id'=> 'nullable|exists:impresoras,id',
        ]);

        $plato = Plato::findOrFail($data['plato_id']);
        $item = PedidoItem::create([
            'pedido_id'      => $pedido->id,
            'plato_id'       => $plato->id,
            'cantidad'       => $data['cantidad'],
            'precio_unitario'=> $plato->precio,
            'notas'          => $data['notas'] ?? null,
            'impresora_id'   => $data['impresora_id'] ?? null,
        ]);

        return response()->json($item->load('plato'), 201);
    }

    public function cerrar(Request $request, Pedido $pedido)
    {
        $pedido->update([
            'estado'       => 'cerrado',
            'fecha_cierre' => now(),
        ]);
        if ($pedido->mesa_id) {
            Mesa::find($pedido->mesa_id)->update(['estado' => 'libre']);
        }
        return response()->json($pedido);
    }
}
