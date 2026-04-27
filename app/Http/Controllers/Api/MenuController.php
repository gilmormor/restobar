<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\UsuarioSucursal;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Retorna el menú dinámico del usuario autenticado.
     * Soporta hasta 3 niveles: padre → hijo → nieto.
     * Superadmin ve todo; otros solo los menús asignados a su rol.
     */
    public function menuParaUsuario(Request $request)
    {
        $user       = $request->user('sanctum');
        $sucursalId = $request->header('X-Sucursal-Id') ?? $request->sucursal_id;

        $pivot = UsuarioSucursal::where('usuario_id', $user->id)
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->first();

        if (!$pivot) {
            return response()->json(['menu' => []]);
        }

        $rol = \App\Models\Rol::find($pivot->rol_id);
        if (!$rol) {
            return response()->json(['menu' => []]);
        }

        if ($rol->es_superadmin) {
            $menus = Menu::with([
                    'hijos' => fn ($q) => $q->where('activo', true)
                                            ->orderBy('orden')
                                            ->with([
                                                'hijos' => fn ($q2) => $q2->where('activo', true)
                                                                           ->orderBy('orden'),
                                            ]),
                ])
                ->whereNull('menus.menu_id')
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        } else {
            $menuIds = $rol->menus()->pluck('menus.id');

            $menus = Menu::with([
                    'hijos' => function ($q) use ($menuIds) {
                        $q->whereIn('menus.id', $menuIds)
                          ->where('menus.activo', true)
                          ->orderBy('menus.orden')
                          ->with([
                              'hijos' => fn ($q2) => $q2->whereIn('menus.id', $menuIds)
                                                         ->where('menus.activo', true)
                                                         ->orderBy('menus.orden'),
                          ]);
                    },
                ])
                ->whereNull('menus.menu_id')
                ->whereIn('menus.id', $menuIds)
                ->where('menus.activo', true)
                ->orderBy('menus.orden')
                ->get();
        }

        return response()->json(['menu' => $menus]);
    }
}
