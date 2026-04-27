<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(
            Empresa::with(['moneda', 'pais'])->orderBy('nombre')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'tipo_id_fiscal'   => 'nullable|string|max:20',
            'id_fiscal'        => 'nullable|string|max:50',
            'giro'             => 'nullable|string|max:200',
            'direccion'        => 'nullable|string',
            'telefono'         => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:100',
            'logo'             => 'nullable|string',
            'moneda_id'        => 'nullable|exists:monedas,id',
            'pais_id'          => 'nullable|exists:paises,id',
            'activa'           => 'boolean',
        ]);

        $empresa = Empresa::create($data);
        return response()->json($empresa->load(['moneda', 'pais']), 201);
    }

    public function show(Empresa $empresa)
    {
        return response()->json($empresa->load(['moneda', 'pais', 'sucursales']));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'nombre'           => 'sometimes|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'tipo_id_fiscal'   => 'nullable|string|max:20',
            'id_fiscal'        => 'nullable|string|max:50',
            'giro'             => 'nullable|string|max:200',
            'direccion'        => 'nullable|string',
            'telefono'         => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:100',
            'logo'             => 'nullable|string',
            'moneda_id'        => 'nullable|exists:monedas,id',
            'pais_id'          => 'nullable|exists:paises,id',
            'activa'           => 'boolean',
        ]);

        $empresa->update($data);
        return response()->json($empresa->load(['moneda', 'pais']));
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->sucursales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la empresa tiene sucursales asociadas.',
            ], 422);
        }
        $empresa->delete();
        return response()->json(['message' => 'Empresa eliminada']);
    }
}
