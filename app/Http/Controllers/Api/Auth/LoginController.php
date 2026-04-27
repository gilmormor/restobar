<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\UsuarioSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usuario'     => 'required|string',
            'password'    => 'required|string',
            'sucursal_id' => 'nullable|integer|exists:sucursales,id',
        ]);

        $user = Usuario::where('usuario', $request->usuario)
            ->where('activo', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        // Determine which branch to log into
        $sucursalId = $request->sucursal_id;

        if (!$sucursalId) {
            // Use principal branch
            $pivot = UsuarioSucursal::where('usuario_id', $user->id)
                ->where('es_principal', true)
                ->where('activo', true)
                ->first();
            $sucursalId = $pivot?->sucursal_id;
        }

        if (!$sucursalId) {
            // Fallback: first assigned branch
            $pivot = UsuarioSucursal::where('usuario_id', $user->id)
                ->where('activo', true)
                ->first();
            $sucursalId = $pivot?->sucursal_id;
        }

        // Get role for this branch
        $rolId = UsuarioSucursal::where('usuario_id', $user->id)
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->value('rol_id');

        $rol = $rolId ? \App\Models\Rol::find($rolId) : null;

        // Delete old tokens and create a fresh one
        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        // Load permission slugs for this role
        $permisos = [];
        if ($rol) {
            $permisos = $rol->es_superadmin
                ? ['*'] // wildcard — frontend grants everything
                : $rol->permisos()->pluck('slug')->toArray();
        }

        $sucursalNombre = $sucursalId
            ? \App\Models\Sucursal::find($sucursalId)?->nombre
            : null;

        return response()->json([
            'token'           => $token,
            'usuario'         => [
                'id'       => $user->id,
                'nombre'   => $user->nombre,
                'apellido' => $user->apellido,
                'usuario'  => $user->usuario,
                'foto'     => $user->foto,
            ],
            'sucursal_id'     => $sucursalId,
            'sucursal_nombre' => $sucursalNombre,
            'rol'             => $rol ? ['id' => $rol->id, 'nombre' => $rol->nombre, 'es_superadmin' => $rol->es_superadmin] : null,
            'permisos'        => $permisos,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user('sanctum')->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request)
    {
        $user = $request->user('sanctum');
        return response()->json($user->load('sucursales'));
    }
}
