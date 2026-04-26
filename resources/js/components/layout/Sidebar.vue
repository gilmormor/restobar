<template>
  <aside class="w-64 min-h-screen bg-gray-900 text-white flex flex-col">
    <!-- Logo -->
    <div class="px-6 py-5 border-b border-gray-700">
      <h1 class="text-xl font-bold text-white flex items-center gap-2">
        🍽️ <span>Restobar</span>
      </h1>
      <p class="text-xs text-gray-400 mt-1">Sistema de gestión</p>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 px-3 py-4 space-y-1">
      <router-link
        v-for="item in menuItems"
        :key="item.route"
        :to="item.route"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150"
        :class="$route.path === item.route
          ? 'bg-orange-500 text-white'
          : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
      >
        <span class="text-lg">{{ item.icon }}</span>
        <span>{{ item.label }}</span>
      </router-link>
    </nav>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-gray-700">
      <p class="text-xs text-gray-500">v1.0.0 — Laravel 13 + Vue 3</p>
    </div>
  </aside>
</template>

<script setup>
const menuItems = [
  { route: '/',           icon: '📊', label: 'Dashboard' },
  { route: '/mesas',      icon: '🪑', label: 'Mesas y Ambientes' },
  { route: '/pedidos',    icon: '📋', label: 'Pedidos / Comandas' },
  { route: '/menu',       icon: '🍕', label: 'Carta / Menú' },
  { route: '/inventario', icon: '📦', label: 'Inventario' },
  { route: '/empleados',  icon: '👥', label: 'Empleados' },
  { route: '/caja',       icon: '💰', label: 'Caja y Facturación' },
  { route: '/reportes',   icon: '📈', label: 'Reportes' },
];
</script>
