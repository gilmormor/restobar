<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Obtener el ID del rol superadmin
$superadminRolId = DB::table('roles')->where('es_superadmin', true)->value('id');
echo "Rol Superadmin ID: $superadminRolId\n";

// Verificar si ya existe el menu admin
$existe = DB::table('menus')->where('slug', 'admin')->exists();
if ($existe) {
    echo "Los menus admin ya existen. Abortando.\n";
    exit(0);
}

// Obtener el mayor orden actual en top-level
$maxOrden = DB::table('menus')->whereNull('menu_id')->max('orden') ?? 0;

// ── NIVEL 0: Administración (padre top-level) ───────────────────────���───────
$adminId = DB::table('menus')->insertGetId([
    'menu_id'    => null,
    'nombre'     => 'Administración',
    'icono'      => 'ShieldCheckIcon',
    'ruta'       => null,
    'slug'       => 'admin',
    'orden'      => $maxOrden + 1,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "Administración ID: $adminId\n";

// ── NIVEL 1: Hijos directos de Administración ───────────────────────��──────
$usuarioId = DB::table('menus')->insertGetId([
    'menu_id'    => $adminId,
    'nombre'     => 'Usuarios',
    'icono'      => 'UserCircleIcon',
    'ruta'       => 'admin-usuario',
    'slug'       => 'admin.usuario',
    'orden'      => 1,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

$menuAdminId = DB::table('menus')->insertGetId([
    'menu_id'    => $adminId,
    'nombre'     => 'Menú',
    'icono'      => 'Bars3Icon',
    'ruta'       => 'admin-menu',
    'slug'       => 'admin.menu',
    'orden'      => 2,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Roles → padre
$rolesPadreId = DB::table('menus')->insertGetId([
    'menu_id'    => $adminId,
    'nombre'     => 'Roles',
    'icono'      => 'KeyIcon',
    'ruta'       => null,
    'slug'       => 'admin.roles-grupo',
    'orden'      => 3,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// Permisos → padre
$permisosPadreId = DB::table('menus')->insertGetId([
    'menu_id'    => $adminId,
    'nombre'     => 'Permisos',
    'icono'      => 'LockClosedIcon',
    'ruta'       => null,
    'slug'       => 'admin.permisos-grupo',
    'orden'      => 4,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Usuarios: $usuarioId | Menu: $menuAdminId | Roles(padre): $rolesPadreId | Permisos(padre): $permisosPadreId\n";

// ── NIVEL 2: Hijos de Roles ─────────────────────────────────────────────────
$rolId = DB::table('menus')->insertGetId([
    'menu_id'    => $rolesPadreId,
    'nombre'     => 'Roles',
    'icono'      => null,
    'ruta'       => 'admin-rol',
    'slug'       => 'admin.rol',
    'orden'      => 1,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

$menuRolId = DB::table('menus')->insertGetId([
    'menu_id'    => $rolesPadreId,
    'nombre'     => 'Menú - Rol',
    'icono'      => null,
    'ruta'       => 'admin-menu-rol',
    'slug'       => 'admin.menu-rol',
    'orden'      => 2,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

// ── NIVEL 2: Hijos de Permisos ──────────────────────────────────────────────
$permisoId = DB::table('menus')->insertGetId([
    'menu_id'    => $permisosPadreId,
    'nombre'     => 'Permisos',
    'icono'      => null,
    'ruta'       => 'admin-permiso',
    'slug'       => 'admin.permiso',
    'orden'      => 1,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

$permisoRolId = DB::table('menus')->insertGetId([
    'menu_id'    => $permisosPadreId,
    'nombre'     => 'Permiso - Rol',
    'icono'      => null,
    'ruta'       => 'admin-permiso-rol',
    'slug'       => 'admin.permiso-rol',
    'orden'      => 2,
    'activo'     => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Rol: $rolId | MenuRol: $menuRolId | Permiso: $permisoId | PermisoRol: $permisoRolId\n";

// ── Asignar TODOS los nuevos menús al rol Superadmin via menu_rol ───────────
// El superadmin es es_superadmin=true, ve TODO sin menu_rol,
// pero igual lo insertamos para consistencia
$todosIds = [$adminId, $usuarioId, $menuAdminId, $rolesPadreId, $permisosPadreId,
             $rolId, $menuRolId, $permisoId, $permisoRolId];

foreach ($todosIds as $mid) {
    DB::table('menu_rol')->insert([
        'menu_id'    => $mid,
        'rol_id'     => $superadminRolId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "OK - Menús admin creados e insertados en menu_rol del superadmin.\n";
echo "IDs: admin=$adminId, usuario=$usuarioId, menu=$menuAdminId\n";
echo "     roles-padre=$rolesPadreId, permisos-padre=$permisosPadreId\n";
echo "     rol=$rolId, menu-rol=$menuRolId, permiso=$permisoId, permiso-rol=$permisoRolId\n";
