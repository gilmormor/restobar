<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AmbienteController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PlatoController;
use App\Http\Controllers\Api\IngredienteController;
use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\FacturaController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\BodegaController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\InventarioCierreController;
use App\Http\Controllers\Api\PermisoController;
use App\Http\Controllers\Api\MenuAdminController;  // CRUD árbol de menús
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\MonedaController;
use App\Http\Controllers\Api\GeoController;

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::post('auth/login',  [LoginController::class, 'login']);
Route::post('auth/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::get('auth/me',      [LoginController::class, 'me'])->middleware('auth:sanctum');

// ─── Protected routes ────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'sucursal'])->group(function () {

    // Dynamic menu for current user
    Route::get('auth/menu', [MenuController::class, 'menuParaUsuario']);

    // Operational
    Route::apiResource('ambientes',    AmbienteController::class);
    Route::apiResource('mesas',        MesaController::class);
    Route::apiResource('categorias',   CategoriaController::class);
    Route::apiResource('platos',       PlatoController::class);
    Route::apiResource('ingredientes', IngredienteController::class);
    Route::apiResource('empleados',    EmpleadoController::class);
    Route::apiResource('pedidos',      PedidoController::class);
    Route::apiResource('cajas',        CajaController::class);
    Route::apiResource('facturas',     FacturaController::class);

    // Admin / config
    Route::apiResource('usuarios',   UsuarioController::class);
    Route::apiResource('roles',      RolController::class);
    Route::apiResource('permisos',   PermisoController::class);
    Route::post('menus/reordenar',   [MenuAdminController::class, 'reordenar']);
    Route::apiResource('menus',      MenuAdminController::class);
    Route::apiResource('sucursales', SucursalController::class);
    Route::apiResource('empresas',   EmpresaController::class);
    Route::apiResource('monedas',    MonedaController::class);

    // Geografía (solo lectura, filtrable)
    Route::get('geo/paises',     [GeoController::class, 'paises']);
    Route::get('geo/regiones',   [GeoController::class, 'regiones']);
    Route::get('geo/provincias', [GeoController::class, 'provincias']);
    Route::get('geo/comunas',    [GeoController::class, 'comunas']);
    Route::apiResource('bodegas',    BodegaController::class);
    Route::get('configuracion',      [ConfiguracionController::class, 'index']);
    Route::put('configuracion',      [ConfiguracionController::class, 'update']);

    // Role permission/menu matrix
    Route::put('roles/{rol}/permisos', [RolController::class, 'sincronizarPermisos']);
    Route::put('roles/{rol}/menus',    [RolController::class, 'sincronizarMenus']);

    // User-branch assignment
    Route::post  ('usuarios/{usuario}/sucursales',            [UsuarioController::class, 'asignarSucursal']);
    Route::delete('usuarios/{usuario}/sucursales/{sucursal}', [UsuarioController::class, 'quitarSucursal']);

    // Inventario: control de períodos (cierre/apertura mensual)
    Route::get ('inventario/control',               [InventarioCierreController::class, 'index']);
    Route::get ('inventario/control/preview',       [InventarioCierreController::class, 'preview']);
    Route::post('inventario/control/apertura',      [InventarioCierreController::class, 'apertura']);
    Route::post('inventario/control/cierre',        [InventarioCierreController::class, 'cierre']);
    Route::post('inventario/control/recalcular',    [InventarioCierreController::class, 'recalcular']);
    Route::get ('inventario/{ingredienteId}/historico', [InventarioCierreController::class, 'historico']);

    // Mesa actions
    Route::patch('mesas/{mesa}/estado',   [MesaController::class, 'cambiarEstado']);
    Route::patch('mesas/{mesa}/posicion', [MesaController::class, 'actualizarPosicion']);

    // Plato actions
    Route::patch('platos/{plato}/disponibilidad', [PlatoController::class, 'cambiarDisponibilidad']);

    // Pedido actions
    Route::get('pedidos/{pedido}/items',  [PedidoController::class, 'items']);
    Route::post('pedidos/{pedido}/items', [PedidoController::class, 'agregarItem']);
    Route::patch('pedidos/{pedido}/cerrar', [PedidoController::class, 'cerrar']);
});
