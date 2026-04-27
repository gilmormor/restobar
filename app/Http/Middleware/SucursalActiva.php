<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SucursalActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        // Leer X-Sucursal-Id del header (enviado por el frontend en cada request)
        $sucursalId = $request->header('X-Sucursal-Id');
        $sucursalId = $sucursalId ? (int) $sucursalId : null;

        $usuario = $request->user('sanctum');
        $esSuperadmin = $usuario?->esSuperadmin() ?? false;

        // sucursalId() → devuelve el ID activo (null si superadmin sin filtro)
        Request::macro('sucursalId', function () use ($sucursalId): ?int {
            return $sucursalId;
        });

        // filtraSucursal() → true cuando se debe aplicar WHERE sucursal_id = ?
        // Superadmin SIN header  → false (ve todas las sucursales)
        // Superadmin CON header  → true  (ve solo la sucursal seleccionada)
        // Usuario normal         → true  (siempre filtra)
        Request::macro('filtraSucursal', function () use ($sucursalId, $esSuperadmin): bool {
            if ($esSuperadmin && $sucursalId === null) return false;
            return true;
        });

        return $next($request);
    }
}
