<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        return response()->json(
            Permiso::with('menu:id,nombre')
                ->orderBy('slug')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'        => 'required|string|max:100|unique:permisos,slug',
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'menu_id'     => 'nullable|exists:menus,id',
        ]);

        $permiso = Permiso::create($data);
        return response()->json($permiso->load('menu:id,nombre'), 201);
    }

    public function show(Permiso $permiso)
    {
        return response()->json($permiso->load('menu:id,nombre'));
    }

    public function update(Request $request, Permiso $permiso)
    {
        $data = $request->validate([
            'slug'        => "sometimes|string|max:100|unique:permisos,slug,{$permiso->id}",
            'nombre'      => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'menu_id'     => 'nullable|exists:menus,id',
        ]);

        $permiso->update($data);
        return response()->json($permiso->load('menu:id,nombre'));
    }

    public function destroy(Permiso $permiso)
    {
        $permiso->delete();
        return response()->json(['message' => 'Permiso eliminado']);
    }
}
