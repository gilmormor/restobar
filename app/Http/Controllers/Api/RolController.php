<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;

class RolController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(
            Rol::with(['permisos', 'menus'])->orderBy('nombre')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string',
            'es_superadmin' => 'boolean',
        ]);
        $rol = Rol::create($data);
        return response()->json($rol, 201);
    }

    public function show(Rol $rol)
    {
        return response()->json($rol->load(['permisos', 'menus']));
    }

    public function update(Request $request, Rol $rol)
    {
        if ($lock = $this->checkLock($request, $rol)) return $lock;

        $rol->update($request->validate([
            'nombre'        => 'sometimes|string|max:100',
            'descripcion'   => 'nullable|string',
            'es_superadmin' => 'boolean',
            'activo'        => 'boolean',
        ]));
        return response()->json($rol->load(['permisos', 'menus']));
    }

    public function destroy(Request $request, Rol $rol)
    {
        if ($lock = $this->checkLock($request, $rol)) return $lock;

        if ($rol->usuarioSucursales()->exists()) {
            return response()->json([
                'error'   => 'has_relations',
                'message' => 'No se puede eliminar: el rol tiene usuarios asignados.',
            ], 422);
        }

        $rol->delete();
        return response()->json(['message' => 'Rol eliminado correctamente']);
    }

    /**
     * Sync permissions for a role.
     * Acepta: { permisos: [1,2,3] } o { permiso_ids: [1,2,3] }
     */
    public function sincronizarPermisos(Request $request, Rol $rol)
    {
        // Acepta ambos campos para compatibilidad
        $ids = $request->input('permisos') ?? $request->input('permiso_ids') ?? [];
        $rol->permisos()->sync($ids);
        return response()->json($rol->load('permisos'));
    }

    /**
     * Sync menu visibility for a role.
     * Acepta: { menus: [1,2,3] } o { menu_ids: [1,2,3] }
     */
    public function sincronizarMenus(Request $request, Rol $rol)
    {
        $ids = $request->input('menus') ?? $request->input('menu_ids') ?? [];
        $rol->menus()->sync($ids);
        return response()->json($rol->load('menus'));
    }
}
