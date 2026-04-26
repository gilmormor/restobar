import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from '../views/Dashboard.vue';
import Mesas from '../views/Mesas.vue';
import Pedidos from '../views/Pedidos.vue';
import Menu from '../views/Menu.vue';
import Inventario from '../views/Inventario.vue';
import Empleados from '../views/Empleados.vue';
import Caja from '../views/Caja.vue';
import Reportes from '../views/Reportes.vue';

const routes = [
    { path: '/',            component: Dashboard,  name: 'dashboard' },
    { path: '/mesas',       component: Mesas,      name: 'mesas' },
    { path: '/pedidos',     component: Pedidos,    name: 'pedidos' },
    { path: '/menu',        component: Menu,       name: 'menu' },
    { path: '/inventario',  component: Inventario, name: 'inventario' },
    { path: '/empleados',   component: Empleados,  name: 'empleados' },
    { path: '/caja',        component: Caja,       name: 'caja' },
    { path: '/reportes',    component: Reportes,   name: 'reportes' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
