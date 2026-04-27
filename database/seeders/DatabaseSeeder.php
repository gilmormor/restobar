<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. SUCURSAL PRINCIPAL ────────────────────────────────────────────
        $sucursalId = DB::table('sucursales')->insertGetId([
            'nombre'     => 'Restobar Principal',
            'direccion'  => 'Calle Principal #123',
            'activa'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── 2. BODEGA PRINCIPAL ──────────────────────────────────────────────
        $bodegaId = DB::table('bodegas')->insertGetId([
            'nombre'     => 'Bodega Principal',
            'descripcion'=> 'Bodega central del restobar',
            'activa'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bodega_sucursal')->insert([
            'bodega_id'   => $bodegaId,
            'sucursal_id' => $sucursalId,
            'es_principal'=> true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ─── 3. ROLES ─────────────────────────────────────────────────────────
        $rolSuperadminId = DB::table('roles')->insertGetId([
            'nombre'        => 'Superadministrador',
            'descripcion'   => 'Acceso total al sistema',
            'es_superadmin' => true,
            'activo'        => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $rolAdminId = DB::table('roles')->insertGetId([
            'nombre'        => 'Administrador',
            'descripcion'   => 'Gestión general del negocio',
            'es_superadmin' => false,
            'activo'        => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $rolCajeroId = DB::table('roles')->insertGetId([
            'nombre'        => 'Cajero',
            'descripcion'   => 'Manejo de caja y facturación',
            'es_superadmin' => false,
            'activo'        => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $rolMesoneroId = DB::table('roles')->insertGetId([
            'nombre'        => 'Mesonero',
            'descripcion'   => 'Atención de mesas y toma de pedidos',
            'es_superadmin' => false,
            'activo'        => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $rolCocineroId = DB::table('roles')->insertGetId([
            'nombre'        => 'Cocinero',
            'descripcion'   => 'Preparación de pedidos en cocina',
            'es_superadmin' => false,
            'activo'        => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // ─── 4. MENÚS (dynamic navigation) ───────────────────────────────────
        $menus = [
            // Main items
            ['slug' => 'dashboard',   'nombre' => 'Dashboard',   'icono' => 'HomeIcon',         'ruta' => 'dashboard',   'orden' => 1,  'menu_id' => null],
            ['slug' => 'mesas',       'nombre' => 'Mesas',       'icono' => 'TableCellsIcon',    'ruta' => 'mesas',       'orden' => 2,  'menu_id' => null],
            ['slug' => 'pedidos',     'nombre' => 'Pedidos',     'icono' => 'ClipboardListIcon',  'ruta' => 'pedidos',     'orden' => 3,  'menu_id' => null],
            ['slug' => 'carta',       'nombre' => 'Carta / Menú','icono' => 'BookOpenIcon',      'ruta' => 'menu',        'orden' => 4,  'menu_id' => null],
            ['slug' => 'cocina',      'nombre' => 'Cocina (KDS)', 'icono' => 'FireIcon',         'ruta' => 'cocina',      'orden' => 5,  'menu_id' => null],
            ['slug' => 'caja',        'nombre' => 'Caja',        'icono' => 'CurrencyDollarIcon', 'ruta' => 'caja',        'orden' => 6,  'menu_id' => null],
            ['slug' => 'inventario',  'nombre' => 'Inventario',  'icono' => 'ArchiveBoxIcon',    'ruta' => null,          'orden' => 7,  'menu_id' => null],
            ['slug' => 'empleados',   'nombre' => 'Empleados',   'icono' => 'UsersIcon',         'ruta' => null,          'orden' => 8,  'menu_id' => null],
            ['slug' => 'reportes',    'nombre' => 'Reportes',    'icono' => 'ChartBarIcon',      'ruta' => null,          'orden' => 9,  'menu_id' => null],
            ['slug' => 'configuracion','nombre'=> 'Configuración','icono'=> 'Cog6ToothIcon',     'ruta' => null,          'orden' => 10, 'menu_id' => null],
        ];

        $menuIds = [];
        foreach ($menus as $menu) {
            $menuIds[$menu['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menu['menu_id'],
                'nombre'     => $menu['nombre'],
                'icono'      => $menu['icono'],
                'ruta'       => $menu['ruta'],
                'slug'       => $menu['slug'],
                'orden'      => $menu['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menus for Inventario
        $subMenusInventario = [
            ['slug' => 'inventario.ingredientes', 'nombre' => 'Ingredientes',     'ruta' => 'ingredientes',  'orden' => 1],
            ['slug' => 'inventario.movimientos',  'nombre' => 'Movimientos',      'ruta' => 'movimientos',   'orden' => 2],
            ['slug' => 'inventario.bodegas',      'nombre' => 'Bodegas',          'ruta' => 'bodegas',       'orden' => 3],
        ];
        foreach ($subMenusInventario as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds['inventario'],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menus for Configuración
        $subMenusConfig = [
            ['slug' => 'config.sucursales',  'nombre' => 'Sucursales',   'ruta' => 'sucursales',   'orden' => 1],
            ['slug' => 'config.usuarios',    'nombre' => 'Usuarios',     'ruta' => 'usuarios',     'orden' => 2],
            ['slug' => 'config.roles',       'nombre' => 'Roles',        'ruta' => 'roles',        'orden' => 3],
            ['slug' => 'config.impresoras',  'nombre' => 'Impresoras',   'ruta' => 'impresoras',   'orden' => 4],
        ];
        foreach ($subMenusConfig as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds['configuracion'],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menus for Empleados
        $subMenusEmpleados = [
            ['slug' => 'empleados.lista',    'nombre' => 'Lista',        'ruta' => 'empleados',    'orden' => 1],
            ['slug' => 'empleados.horarios', 'nombre' => 'Horarios',     'ruta' => 'horarios',     'orden' => 2],
        ];
        foreach ($subMenusEmpleados as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds['empleados'],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sub-menus for Reportes
        $subMenusReportes = [
            ['slug' => 'reportes.ventas',    'nombre' => 'Ventas',       'ruta' => 'reportes.ventas',    'orden' => 1],
            ['slug' => 'reportes.inventario','nombre' => 'Inventario',   'ruta' => 'reportes.inventario','orden' => 2],
            ['slug' => 'reportes.caja',      'nombre' => 'Caja',         'ruta' => 'reportes.caja',      'orden' => 3],
        ];
        foreach ($subMenusReportes as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds['reportes'],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Administración (módulo admin — solo superadmin) ───────────────────
        $menuIds['admin'] = DB::table('menus')->insertGetId([
            'menu_id'    => null,
            'nombre'     => 'Administración',
            'icono'      => 'ShieldCheckIcon',
            'ruta'       => null,
            'slug'       => 'admin',
            'orden'      => 11,
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hijos directos de Administración
        foreach ([
            ['slug' => 'admin.usuario',         'nombre' => 'Usuarios',   'icono' => 'UserCircleIcon', 'ruta' => 'admin-usuario',   'orden' => 1, 'padre' => 'admin'],
            ['slug' => 'admin.menu',             'nombre' => 'Menú',       'icono' => 'Bars3Icon',      'ruta' => 'admin-menu',      'orden' => 2, 'padre' => 'admin'],
            ['slug' => 'admin.roles-grupo',      'nombre' => 'Roles',      'icono' => 'KeyIcon',        'ruta' => null,              'orden' => 3, 'padre' => 'admin'],
            ['slug' => 'admin.permisos-grupo',   'nombre' => 'Permisos',   'icono' => 'LockClosedIcon', 'ruta' => null,              'orden' => 4, 'padre' => 'admin'],
        ] as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds[$sub['padre']],
                'nombre'     => $sub['nombre'],
                'icono'      => $sub['icono'],
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hijos de Roles
        foreach ([
            ['slug' => 'admin.rol',       'nombre' => 'Roles',       'ruta' => 'admin-rol',       'orden' => 1, 'padre' => 'admin.roles-grupo'],
            ['slug' => 'admin.menu-rol',  'nombre' => 'Menú - Rol',  'ruta' => 'admin-menu-rol',  'orden' => 2, 'padre' => 'admin.roles-grupo'],
        ] as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds[$sub['padre']],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hijos de Permisos
        foreach ([
            ['slug' => 'admin.permiso',     'nombre' => 'Permisos',       'ruta' => 'admin-permiso',     'orden' => 1, 'padre' => 'admin.permisos-grupo'],
            ['slug' => 'admin.permiso-rol', 'nombre' => 'Permiso - Rol',  'ruta' => 'admin-permiso-rol', 'orden' => 2, 'padre' => 'admin.permisos-grupo'],
        ] as $sub) {
            $menuIds[$sub['slug']] = DB::table('menus')->insertGetId([
                'menu_id'    => $menuIds[$sub['padre']],
                'nombre'     => $sub['nombre'],
                'icono'      => null,
                'ruta'       => $sub['ruta'],
                'slug'       => $sub['slug'],
                'orden'      => $sub['orden'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ─── 5. PERMISOS ──────────────────────────────────────────────────────
        $permisosData = [
            // Mesas
            ['menu' => 'mesas',       'nombre' => 'Ver mesas',              'slug' => 'mesas.ver'],
            ['menu' => 'mesas',       'nombre' => 'Crear mesa',             'slug' => 'mesas.crear'],
            ['menu' => 'mesas',       'nombre' => 'Editar mesa',            'slug' => 'mesas.editar'],
            ['menu' => 'mesas',       'nombre' => 'Eliminar mesa',          'slug' => 'mesas.eliminar'],
            ['menu' => 'mesas',       'nombre' => 'Cambiar estado mesa',    'slug' => 'mesas.cambiar-estado'],
            // Pedidos
            ['menu' => 'pedidos',     'nombre' => 'Ver pedidos',            'slug' => 'pedidos.ver'],
            ['menu' => 'pedidos',     'nombre' => 'Crear pedido',           'slug' => 'pedidos.crear'],
            ['menu' => 'pedidos',     'nombre' => 'Editar pedido',          'slug' => 'pedidos.editar'],
            ['menu' => 'pedidos',     'nombre' => 'Anular pedido',          'slug' => 'pedidos.anular'],
            ['menu' => 'pedidos',     'nombre' => 'Aplicar descuento',      'slug' => 'pedidos.aplicar-descuento'],
            // Carta
            ['menu' => 'carta',       'nombre' => 'Ver carta',              'slug' => 'carta.ver'],
            ['menu' => 'carta',       'nombre' => 'Crear categoría',        'slug' => 'carta.categorias.crear'],
            ['menu' => 'carta',       'nombre' => 'Editar categoría',       'slug' => 'carta.categorias.editar'],
            ['menu' => 'carta',       'nombre' => 'Eliminar categoría',     'slug' => 'carta.categorias.eliminar'],
            ['menu' => 'carta',       'nombre' => 'Crear plato',            'slug' => 'carta.platos.crear'],
            ['menu' => 'carta',       'nombre' => 'Editar plato',           'slug' => 'carta.platos.editar'],
            ['menu' => 'carta',       'nombre' => 'Eliminar plato',         'slug' => 'carta.platos.eliminar'],
            ['menu' => 'carta',       'nombre' => 'Cambiar disponibilidad', 'slug' => 'carta.platos.disponibilidad'],
            // Caja
            ['menu' => 'caja',        'nombre' => 'Ver caja',               'slug' => 'caja.ver'],
            ['menu' => 'caja',        'nombre' => 'Abrir caja',             'slug' => 'caja.abrir'],
            ['menu' => 'caja',        'nombre' => 'Cerrar caja',            'slug' => 'caja.cerrar'],
            ['menu' => 'caja',        'nombre' => 'Ver totales',            'slug' => 'caja.ver-totales'],
            ['menu' => 'caja',        'nombre' => 'Generar factura',        'slug' => 'caja.facturar'],
            ['menu' => 'caja',        'nombre' => 'Anular factura',         'slug' => 'caja.anular-factura'],
            // Cocina
            ['menu' => 'cocina',      'nombre' => 'Ver cocina (KDS)',       'slug' => 'cocina.ver'],
            ['menu' => 'cocina',      'nombre' => 'Marcar pedido listo',    'slug' => 'cocina.marcar-listo'],
            // Inventario
            ['menu' => 'inventario',  'nombre' => 'Ver inventario',         'slug' => 'inventario.ver'],
            ['menu' => 'inventario',  'nombre' => 'Crear ingrediente',      'slug' => 'inventario.ingredientes.crear'],
            ['menu' => 'inventario',  'nombre' => 'Editar ingrediente',     'slug' => 'inventario.ingredientes.editar'],
            ['menu' => 'inventario',  'nombre' => 'Eliminar ingrediente',   'slug' => 'inventario.ingredientes.eliminar'],
            ['menu' => 'inventario',  'nombre' => 'Registrar entrada',      'slug' => 'inventario.movimientos.entrada'],
            ['menu' => 'inventario',  'nombre' => 'Registrar ajuste',       'slug' => 'inventario.movimientos.ajuste'],
            ['menu' => 'inventario',  'nombre' => 'Registrar merma',        'slug' => 'inventario.movimientos.merma'],
            // Empleados
            ['menu' => 'empleados',   'nombre' => 'Ver empleados',          'slug' => 'empleados.ver'],
            ['menu' => 'empleados',   'nombre' => 'Crear empleado',         'slug' => 'empleados.crear'],
            ['menu' => 'empleados',   'nombre' => 'Editar empleado',        'slug' => 'empleados.editar'],
            ['menu' => 'empleados',   'nombre' => 'Eliminar empleado',      'slug' => 'empleados.eliminar'],
            // Reportes
            ['menu' => 'reportes',    'nombre' => 'Ver reportes',           'slug' => 'reportes.ver'],
            ['menu' => 'reportes',    'nombre' => 'Exportar reportes',      'slug' => 'reportes.exportar'],
            // Configuración
            ['menu' => 'configuracion','nombre'=> 'Ver configuración',      'slug' => 'config.ver'],
            ['menu' => 'configuracion','nombre'=> 'Editar configuración',   'slug' => 'config.editar'],
            ['menu' => 'configuracion','nombre'=> 'Gestionar usuarios',     'slug' => 'config.usuarios'],
            ['menu' => 'configuracion','nombre'=> 'Gestionar roles',        'slug' => 'config.roles'],
            ['menu' => 'configuracion','nombre'=> 'Gestionar sucursales',   'slug' => 'config.sucursales'],
        ];

        $permisoIds = [];
        foreach ($permisosData as $p) {
            $menuRef = $menuIds[$p['menu']] ?? null;
            $permisoIds[$p['slug']] = DB::table('permisos')->insertGetId([
                'menu_id'     => $menuRef,
                'nombre'      => $p['nombre'],
                'slug'        => $p['slug'],
                'descripcion' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── 6. MENU_ROL — assign menus to roles ─────────────────────────────
        // Admin: all menus except superadmin-only
        $menusAdmin = ['dashboard','mesas','pedidos','carta','cocina','caja',
                       'inventario','empleados','reportes','configuracion'];
        foreach ($menusAdmin as $slug) {
            if (isset($menuIds[$slug])) {
                DB::table('menu_rol')->insert([
                    'menu_id' => $menuIds[$slug], 'rol_id' => $rolAdminId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Cajero: dashboard, mesas (read), pedidos, caja, reportes
        $menusCajero = ['dashboard','mesas','pedidos','caja','reportes'];
        foreach ($menusCajero as $slug) {
            if (isset($menuIds[$slug])) {
                DB::table('menu_rol')->insert([
                    'menu_id' => $menuIds[$slug], 'rol_id' => $rolCajeroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Mesonero: dashboard, mesas, pedidos, carta (read)
        $menusMesonero = ['dashboard','mesas','pedidos','carta'];
        foreach ($menusMesonero as $slug) {
            if (isset($menuIds[$slug])) {
                DB::table('menu_rol')->insert([
                    'menu_id' => $menuIds[$slug], 'rol_id' => $rolMesoneroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Cocinero: dashboard, cocina
        $menusCocinero = ['dashboard','cocina'];
        foreach ($menusCocinero as $slug) {
            if (isset($menuIds[$slug])) {
                DB::table('menu_rol')->insert([
                    'menu_id' => $menuIds[$slug], 'rol_id' => $rolCocineroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // ─── 7. PERMISO_ROL — assign permissions to roles ────────────────────
        // Admin: all permissions
        foreach ($permisoIds as $permId) {
            DB::table('permiso_rol')->insert([
                'permiso_id' => $permId, 'rol_id' => $rolAdminId,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Cajero permissions
        $permisosCajero = [
            'mesas.ver','mesas.cambiar-estado',
            'pedidos.ver','pedidos.crear','pedidos.editar','pedidos.anular',
            'carta.ver',
            'caja.ver','caja.abrir','caja.cerrar','caja.ver-totales','caja.facturar',
            'reportes.ver',
        ];
        foreach ($permisosCajero as $slug) {
            if (isset($permisoIds[$slug])) {
                DB::table('permiso_rol')->insert([
                    'permiso_id' => $permisoIds[$slug], 'rol_id' => $rolCajeroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Mesonero permissions
        $permisosMessonero = [
            'mesas.ver','mesas.cambiar-estado',
            'pedidos.ver','pedidos.crear','pedidos.editar',
            'carta.ver',
        ];
        foreach ($permisosMessonero as $slug) {
            if (isset($permisoIds[$slug])) {
                DB::table('permiso_rol')->insert([
                    'permiso_id' => $permisoIds[$slug], 'rol_id' => $rolMesoneroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Cocinero permissions
        $permisosCocinero = ['cocina.ver','cocina.marcar-listo'];
        foreach ($permisosCocinero as $slug) {
            if (isset($permisoIds[$slug])) {
                DB::table('permiso_rol')->insert([
                    'permiso_id' => $permisoIds[$slug], 'rol_id' => $rolCocineroId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // ─── 8. SUPERADMIN USER ───────────────────────────────────────────────
        $adminUserId = DB::table('usuarios')->insertGetId([
            'nombre'     => 'Super',
            'apellido'   => 'Admin',
            'usuario'    => 'admin',
            'email'      => 'admin@restobar.com',
            'password'   => Hash::make('admin123'),
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('usuario_sucursal')->insert([
            'usuario_id'  => $adminUserId,
            'sucursal_id' => $sucursalId,
            'rol_id'      => $rolSuperadminId,
            'es_principal'=> true,
            'activo'      => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ─── 9. CONFIGURACIÓN INICIAL ─────────────────────────────────────────
        $configItems = [
            ['clave' => 'moneda',              'valor' => 'COP', 'descripcion' => 'Código de moneda'],
            ['clave' => 'simbolo_moneda',      'valor' => '$',   'descripcion' => 'Símbolo de moneda'],
            ['clave' => 'impuesto_porcentaje', 'valor' => '0',   'descripcion' => 'Porcentaje de impuesto (IVA)'],
            ['clave' => 'nombre_negocio',      'valor' => 'Restobar', 'descripcion' => 'Nombre del negocio'],
            ['clave' => 'timezone',            'valor' => 'America/Bogota', 'descripcion' => 'Zona horaria'],
        ];
        foreach ($configItems as $item) {
            DB::table('configuracion')->insert([
                'sucursal_id' => null, // global config
                'clave'       => $item['clave'],
                'valor'       => $item['valor'],
                'descripcion' => $item['descripcion'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── 10. AMBIENTE Y MESAS DE EJEMPLO ─────────────────────────────────
        $ambienteId = DB::table('ambientes')->insertGetId([
            'sucursal_id' => $sucursalId,
            'nombre'      => 'Salón Principal',
            'descripcion' => 'Área principal del restaurante',
            'activo'      => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        for ($i = 1; $i <= 8; $i++) {
            DB::table('mesas')->insert([
                'ambiente_id' => $ambienteId,
                'numero'      => $i,
                'capacidad'   => 4,
                'pos_x'       => (($i - 1) % 4) * 160 + 20,
                'pos_y'       => floor(($i - 1) / 4) * 140 + 20,
                'estado'      => 'libre',
                'activa'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── 11. CATEGORÍAS Y PLATOS DE EJEMPLO ──────────────────────────────
        $categorias = [
            ['nombre' => 'Entradas',   'icono' => '🥗', 'orden' => 1],
            ['nombre' => 'Principales','icono' => '🍽️', 'orden' => 2],
            ['nombre' => 'Bebidas',    'icono' => '🥤', 'orden' => 3],
            ['nombre' => 'Postres',    'icono' => '🍰', 'orden' => 4],
        ];

        $catIds = [];
        foreach ($categorias as $cat) {
            $catIds[$cat['nombre']] = DB::table('categorias')->insertGetId([
                'nombre'     => $cat['nombre'],
                'icono'      => $cat['icono'],
                'orden'      => $cat['orden'],
                'activa'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $platos = [
            ['cat' => 'Entradas',    'nombre' => 'Tabla de Quesos',    'precio' => 18000],
            ['cat' => 'Entradas',    'nombre' => 'Alitas BBQ',         'precio' => 22000],
            ['cat' => 'Principales', 'nombre' => 'Bandeja Paisa',      'precio' => 35000],
            ['cat' => 'Principales', 'nombre' => 'Filete de Res',      'precio' => 42000],
            ['cat' => 'Principales', 'nombre' => 'Pasta Carbonara',    'precio' => 28000],
            ['cat' => 'Bebidas',     'nombre' => 'Limonada Natural',   'precio' => 8000],
            ['cat' => 'Bebidas',     'nombre' => 'Jugo del Día',       'precio' => 7000],
            ['cat' => 'Bebidas',     'nombre' => 'Cerveza Nacional',   'precio' => 6000],
            ['cat' => 'Postres',     'nombre' => 'Tres Leches',        'precio' => 12000],
            ['cat' => 'Postres',     'nombre' => 'Brownie con Helado', 'precio' => 14000],
        ];

        foreach ($platos as $plato) {
            DB::table('platos')->insert([
                'categoria_id' => $catIds[$plato['cat']],
                'nombre'       => $plato['nombre'],
                'precio'       => $plato['precio'],
                'disponible'   => true,
                'activo'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ─── 12. APERTURA INICIAL DE INVENTARIO ──────────────────────────────
        // El mes actual queda abierto para que se puedan registrar movimientos
        DB::table('inv_control')->insert([
            'annomes'     => date('Ym'),   // período actual, ej: 202604
            'sucursal_id' => $sucursalId,
            'status'      => 0,            // 0 = abierto
            'usuario_id'  => $adminUserId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ─── 13. TIPOS DE MOVIMIENTO DE INVENTARIO ────────────────────────────
        // tipomov: 1 = suma al stock (entrada), -1 = resta al stock (salida)
        // es_apertura_mes: true = este tipo marca el saldo inicial del período
        $tiposMovimiento = [
            ['nombre' => 'Saldo Inicial',       'tipomov' =>  1, 'es_apertura_mes' => true,
             'descripcion' => 'Saldo de apertura generado en el cierre de mes'],
            ['nombre' => 'Compra',              'tipomov' =>  1, 'es_apertura_mes' => false,
             'descripcion' => 'Entrada de mercancía por compra a proveedor'],
            ['nombre' => 'Venta',               'tipomov' => -1, 'es_apertura_mes' => false,
             'descripcion' => 'Salida automática al vender un plato'],
            ['nombre' => 'Ajuste Positivo',     'tipomov' =>  1, 'es_apertura_mes' => false,
             'descripcion' => 'Ajuste manual que incrementa el stock (conteo físico)'],
            ['nombre' => 'Ajuste Negativo',     'tipomov' => -1, 'es_apertura_mes' => false,
             'descripcion' => 'Ajuste manual que disminuye el stock (conteo físico)'],
            ['nombre' => 'Merma',               'tipomov' => -1, 'es_apertura_mes' => false,
             'descripcion' => 'Pérdida por deterioro, vencimiento o accidente'],
            ['nombre' => 'Transferencia Entrada','tipomov' =>  1, 'es_apertura_mes' => false,
             'descripcion' => 'Ingreso de producto recibido desde otra bodega'],
            ['nombre' => 'Transferencia Salida','tipomov' => -1, 'es_apertura_mes' => false,
             'descripcion' => 'Salida de producto enviado a otra bodega'],
        ];

        foreach ($tiposMovimiento as $tipo) {
            DB::table('mov_inv_tipos')->insert([
                'nombre'         => $tipo['nombre'],
                'descripcion'    => $tipo['descripcion'],
                'tipomov'        => $tipo['tipomov'],
                'es_apertura_mes'=> $tipo['es_apertura_mes'],
                'activo'         => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // ─── 14. MÓDULOS DE MOVIMIENTO DE INVENTARIO ─────────────────────────
        // Identifica qué módulo del sistema originó el movimiento
        $modulos = [
            ['nombre' => 'Manual',       'descripcion' => 'Movimiento ingresado manualmente por el usuario'],
            ['nombre' => 'Venta',        'descripcion' => 'Deducción automática al procesar una venta'],
            ['nombre' => 'Compra',       'descripcion' => 'Entrada registrada al recibir una compra'],
            ['nombre' => 'Ajuste',       'descripcion' => 'Ajuste de inventario por conteo físico'],
            ['nombre' => 'Transferencia','descripcion' => 'Movimiento entre bodegas'],
            ['nombre' => 'Cierre Mes',   'descripcion' => 'Saldo inicial generado en el cierre mensual'],
        ];

        foreach ($modulos as $modulo) {
            DB::table('mov_inv_modulos')->insert([
                'nombre'      => $modulo['nombre'],
                'descripcion' => $modulo['descripcion'],
                'activo'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── 14. DATOS GEOGRÁFICOS Y EMPRESARIALES ────────────────────────────
        $this->call([
            PaisesYMonedasSeeder::class,
            ChileGeoSeeder::class,
            ColombiaGeoSeeder::class,
        ]);
    }
}
