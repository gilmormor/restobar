<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\UsuarioSucursal;
use App\Traits\OptimisticLocking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class UsuarioController extends Controller
{
    use OptimisticLocking;

    public function index()
    {
        return response()->json(
            Usuario::with(['sucursales' => fn($q) => $q->withPivot('rol_id', 'es_principal', 'activo')])
                ->orderBy('apellido')->orderBy('nombre')
                ->get()
                ->makeHidden('password')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'usuario'         => 'required|string|max:50|unique:usuarios,usuario',
            'email'           => 'nullable|email|unique:usuarios,email',
            'password'        => 'required|string|min:6',
            'foto'            => 'nullable|string',
            'activo'          => 'boolean',
            // Optional: assign to a branch on creation
            'sucursal_id'     => 'nullable|exists:sucursales,id',
            'rol_id'          => 'nullable|exists:roles,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $sucursalId = $data['sucursal_id'] ?? null;
        $rolId      = $data['rol_id'] ?? null;
        unset($data['sucursal_id'], $data['rol_id']);

        $usuario = Usuario::create($data);

        if ($sucursalId && $rolId) {
            UsuarioSucursal::create([
                'usuario_id'  => $usuario->id,
                'sucursal_id' => $sucursalId,
                'rol_id'      => $rolId,
                'es_principal'=> true,
                'activo'      => true,
            ]);
        }

        return response()->json($usuario->load('sucursales')->makeHidden('password'), 201);
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario->load('sucursales')->makeHidden('password'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        if ($lock = $this->checkLock($request, $usuario)) return $lock;

        $data = $request->validate([
            'nombre'   => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'usuario'  => "sometimes|string|max:50|unique:usuarios,usuario,{$usuario->id}",
            'email'    => "nullable|email|unique:usuarios,email,{$usuario->id}",
            'password' => 'nullable|string|min:6',
            'foto'     => 'nullable|string',
            'activo'   => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);
        return response()->json($usuario->load('sucursales')->makeHidden('password'));
    }

    public function destroy(Request $request, Usuario $usuario)
    {
        if ($lock = $this->checkLock($request, $usuario)) return $lock;

        try {
            $usuario->delete();
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error'   => 'has_relations',
                    'message' => 'No se puede eliminar: el usuario tiene registros asociados.',
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Assign (or update) a user to a branch with a role
     */
    public function asignarSucursal(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'sucursal_id'  => 'required|exists:sucursales,id',
            'rol_id'       => 'required|exists:roles,id',
            'es_principal' => 'boolean',
            'activo'       => 'boolean',
        ]);

        // If setting as principal, unset es_principal on other branches first
        if (!empty($data['es_principal'])) {
            UsuarioSucursal::where('usuario_id', $usuario->id)
                ->where('sucursal_id', '!=', $data['sucursal_id'])
                ->update(['es_principal' => false]);
        }

        $pivot = UsuarioSucursal::updateOrCreate(
            ['usuario_id' => $usuario->id, 'sucursal_id' => $data['sucursal_id']],
            [
                'rol_id'       => $data['rol_id'],
                'es_principal' => $data['es_principal'] ?? false,
                'activo'       => $data['activo'] ?? true,
            ]
        );

        return response()->json($pivot);
    }

    /**
     * Remove a user-branch assignment
     */
    public function quitarSucursal(Usuario $usuario, $sucursalId)
    {
        UsuarioSucursal::where('usuario_id', $usuario->id)
            ->where('sucursal_id', $sucursalId)
            ->delete();

        return response()->json(['message' => 'Asignación eliminada']);
    }
}
