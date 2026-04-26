<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">

      <!-- Navbar superior -->
      <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">{{ paginaActual }}</h2>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-500">{{ fecha }}</span>
          <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white text-sm font-bold">
            A
          </div>
        </div>
      </header>

      <!-- Vista del módulo activo -->
      <main class="flex-1 p-6">
        <router-view />
      </main>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from './components/layout/Sidebar.vue';

const route = useRoute();

const paginas = {
  '/':           'Dashboard',
  '/mesas':      'Mesas y Ambientes',
  '/pedidos':    'Pedidos / Comandas',
  '/menu':       'Carta / Menú',
  '/inventario': 'Inventario',
  '/empleados':  'Empleados',
  '/caja':       'Caja y Facturación',
  '/reportes':   'Reportes',
};

const paginaActual = computed(() => paginas[route.path] || 'Restobar');

const fecha = new Date().toLocaleDateString('es-ES', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});
</script>
