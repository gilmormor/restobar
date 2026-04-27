import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

// Rutas públicas
import Login from '../views/Login.vue';

// Rutas protegidas (lazy load)
const Dashboard  = () => import('../views/Dashboard.vue');
const Mesas      = () => import('../views/Mesas.vue');
const Pedidos    = () => import('../views/Pedidos.vue');
const Menu       = () => import('../views/Menu.vue');
const Inventario = () => import('../views/Inventario.vue');
const Empleados  = () => import('../views/Empleados.vue');
const Caja       = () => import('../views/Caja.vue');
const Reportes   = () => import('../views/Reportes.vue');
const Cocina     = () => import('../views/Cocina.vue');

// ── Admin (lazy load) ─────────────────────────────────────────────────────────
const AdminUsuario   = () => import('../views/admin/AdminUsuario.vue');
const AdminMenu      = () => import('../views/admin/AdminMenu.vue');
const AdminRol       = () => import('../views/admin/AdminRol.vue');
const AdminMenuRol   = () => import('../views/admin/AdminMenuRol.vue');
const AdminPermiso    = () => import('../views/admin/AdminPermiso.vue');
const AdminPermisoRol = () => import('../views/admin/AdminPermisoRol.vue');
const AdminEmpresa    = () => import('../views/admin/AdminEmpresa.vue');
const AdminMoneda     = () => import('../views/admin/AdminMoneda.vue');
const AdminSucursal   = () => import('../views/admin/AdminSucursal.vue');

const routes = [
    // ── Pública ──────────────────────────────────────────────────────────────
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { publica: true },
    },

    // ── Protegidas ────────────────────────────────────────────────────────────
    { path: '/',           name: 'dashboard',  component: Dashboard  },
    { path: '/mesas',      name: 'mesas',      component: Mesas      },
    { path: '/pedidos',    name: 'pedidos',    component: Pedidos    },
    { path: '/menu',       name: 'menu',       component: Menu       },
    { path: '/cocina',     name: 'cocina',     component: Cocina     },
    { path: '/inventario', name: 'inventario', component: Inventario },
    { path: '/empleados',  name: 'empleados',  component: Empleados  },
    { path: '/caja',       name: 'caja',       component: Caja       },
    { path: '/reportes',   name: 'reportes',   component: Reportes   },

    // ── Admin ─────────────────────────────────────────────────────────────────
    { path: '/admin/usuario',     name: 'admin-usuario',     component: AdminUsuario    },
    { path: '/admin/menu',        name: 'admin-menu',        component: AdminMenu       },
    { path: '/admin/rol',         name: 'admin-rol',         component: AdminRol        },
    { path: '/admin/menu-rol',    name: 'admin-menu-rol',    component: AdminMenuRol    },
    { path: '/admin/permiso',     name: 'admin-permiso',     component: AdminPermiso    },
    { path: '/admin/permiso-rol', name: 'admin-permiso-rol', component: AdminPermisoRol },
    { path: '/admin/empresa',     name: 'admin-empresa',     component: AdminEmpresa    },
    { path: '/admin/moneda',      name: 'admin-moneda',      component: AdminMoneda     },
    { path: '/admin/sucursal',    name: 'admin-sucursal',    component: AdminSucursal   },

    // ── Catch-all ─────────────────────────────────────────────────────────────
    { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// ── Guard global ──────────────────────────────────────────────────────────────
router.beforeEach((to) => {
    const auth = useAuthStore();

    // Ruta pública: si ya está autenticado redirigir al dashboard
    if (to.meta.publica) {
        if (auth.estaAutenticado) return { name: 'dashboard' };
        return true;
    }

    // Ruta protegida: si no está autenticado ir al login
    if (!auth.estaAutenticado) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    return true;
});

export default router;
