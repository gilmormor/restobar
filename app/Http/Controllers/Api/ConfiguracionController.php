<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Get all global + optional branch-specific config as a flat key→value map
     */
    public function index(Request $request)
    {
        $sucursalId = $request->sucursal_id ?? null;

        $global = Configuracion::whereNull('sucursal_id')->pluck('valor', 'clave');

        if ($sucursalId) {
            $branch = Configuracion::where('sucursal_id', $sucursalId)->pluck('valor', 'clave');
            $merged = $global->merge($branch); // branch overrides global
        } else {
            $merged = $global;
        }

        return response()->json($merged);
    }

    /**
     * Update config keys (upsert). Accepts: { clave: valor, ... } or { sucursal_id: N, items: [{clave,valor},...] }
     */
    public function update(Request $request)
    {
        $sucursalId = $request->sucursal_id ?? null;

        $items = $request->has('items') ? $request->items : $request->except('sucursal_id', 'updated_at');

        foreach ($items as $clave => $valor) {
            Configuracion::updateOrCreate(
                ['sucursal_id' => $sucursalId, 'clave' => $clave],
                ['valor' => $valor]
            );
        }

        return $this->index($request);
    }
}
