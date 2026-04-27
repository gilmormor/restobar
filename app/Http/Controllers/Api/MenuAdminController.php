<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuAdminController extends Controller
{
    /**
     * Retorna todos los menús en árbol (3 niveles).
     */
    public function index()
    {
        $menus = Menu::with([
                'hijos' => fn ($q) => $q->orderBy('orden')
                    ->with(['hijos' => fn ($q2) => $q2->orderBy('orden')]),
            ])
            ->whereNull('menu_id')
            ->orderBy('orden')
            ->get();

        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:100',
            'slug'    => 'nullable|string|max:100',
            'ruta'    => 'nullable|string|max:100',
            'icono'   => 'nullable|string|max:100',
            'menu_id' => 'nullable|exists:menus,id',
            'orden'   => 'integer|min:1',
            'activo'  => 'boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nombre']);
        }

        $menu = Menu::create($data);
        return response()->json($menu, 201);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu->load('hijos'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'nombre'  => 'sometimes|string|max:100',
            'slug'    => 'nullable|string|max:100',
            'ruta'    => 'nullable|string|max:100',
            'icono'   => 'nullable|string|max:100',
            'menu_id' => 'nullable|exists:menus,id',
            'orden'   => 'integer|min:1',
            'activo'  => 'boolean',
        ]);

        $menu->update($data);
        return response()->json($menu);
    }

    /**
     * Recibe [{id, menu_id, orden}] y actualiza toda la jerarquía de una vez.
     */
    public function reordenar(Request $request)
    {
        $items = $request->validate([
            'items'         => 'required|array',
            'items.*.id'    => 'required|integer|exists:menus,id',
            'items.*.menu_id' => 'nullable|integer|exists:menus,id',
            'items.*.orden' => 'required|integer|min:1',
        ])['items'];

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                Menu::where('id', $item['id'])->update([
                    'menu_id' => $item['menu_id'] ?? null,
                    'orden'   => $item['orden'],
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function destroy(Menu $menu)
    {
        if ($menu->hijos()->exists()) {
            return response()->json([
                'error'   => 'has_children',
                'message' => 'No se puede eliminar: el menú tiene submenús.',
            ], 422);
        }
        $menu->delete();
        return response()->json(['message' => 'Menú eliminado']);
    }
}
