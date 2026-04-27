<template>
  <!-- Login: pantalla completa sin layout -->
  <template v-if="!auth.estaAutenticado">
    <router-view />
    <TopProgressBar />
  </template>

  <!-- App principal: solo cuando está autenticado -->
  <div v-else class="flex min-h-screen bg-gray-100">

    <!-- Sidebar - solo PC (md+) -->
    <Sidebar :collapsed="sidebarCollapsed" />

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col min-w-0">

      <!-- Navbar superior -->
      <header class="bg-white shadow-sm px-4 py-0 flex items-center justify-between h-16 flex-shrink-0">

        <!-- Botón hamburguesa (solo md+) -->
        <button
          @click="toggleSidebar"
          class="hidden md:flex w-10 h-10 items-center justify-center rounded-lg
                 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors flex-shrink-0"
          :title="sidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Título de la página -->
        <h2 class="text-base md:text-lg font-semibold text-gray-700 md:ml-3 flex-1 truncate">
          {{ paginaActual }}
        </h2>

        <!-- Usuario + fecha + logout -->
        <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
          <span class="hidden lg:block text-sm text-gray-500">{{ fecha }}</span>

          <!-- Sucursal activa -->
          <div v-if="auth.sucursalNombre"
               class="hidden md:flex items-center gap-1.5 bg-orange-50 border border-orange-200
                      text-orange-700 text-xs font-medium px-2.5 py-1 rounded-lg">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ auth.sucursalNombre }}
          </div>

          <!-- Info usuario -->
          <div class="flex items-center gap-2">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-medium text-gray-700 leading-none">{{ auth.usuario?.nombre }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ auth.rol?.nombre }}</p>
            </div>
            <!-- Avatar -->
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center
                        text-white text-sm font-bold flex-shrink-0 select-none">
              {{ iniciales }}
            </div>
          </div>

          <!-- Botón logout -->
          <button
            @click="handleLogout"
            :disabled="cerrandoSesion"
            title="Cerrar sesión"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                   hover:bg-red-50 hover:text-red-500 transition-colors flex-shrink-0"
          >
            <svg v-if="cerrandoSesion" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </header>

      <!-- Vista del módulo activo -->
      <main class="flex-1 p-4 md:p-6 pb-20 md:pb-6 overflow-auto">
        <router-view />
      </main>

    </div>

    <!-- Bottom Navigation - solo móvil (<md) -->
    <BottomNav />

    <!-- Barra de progreso global -->
    <TopProgressBar />

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth.js';
import Sidebar from './components/layout/Sidebar.vue';
import BottomNav from './components/layout/BottomNav.vue';
import TopProgressBar from './components/ui/TopProgressBar.vue';

const route  = useRoute();
const router = useRouter();
const auth   = useAuthStore();

// ── Sidebar ───────────────────────────────────────────────────────────────────
const sidebarCollapsed = ref(localStorage.getItem('sidebar_collapsed') === 'true');
function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebar_collapsed', sidebarCollapsed.value);
}

// ── Título de página (dinámico desde el menú del store) ───────────────────────
const paginaActual = computed(() => {
    // Busca en ítems de primer nivel y sus hijos
    for (const item of auth.menuItems) {
        if (item.ruta === route.name) return item.nombre;
        if (item.hijos?.length) {
            const hijo = item.hijos.find(h => h.ruta === route.name);
            if (hijo) return hijo.nombre;
        }
    }
    // Fallback por nombre de ruta
    const fallbacks = {
        dashboard:  'Dashboard',
        mesas:      'Mesas y Ambientes',
        pedidos:    'Pedidos / Comandas',
        menu:       'Carta / Menú',
        inventario: 'Inventario',
        empleados:  'Empleados',
        caja:       'Caja y Facturación',
        reportes:   'Reportes',
    };
    return fallbacks[route.name] || 'Restobar';
});

// ── Fecha ─────────────────────────────────────────────────────────────────────
const fecha = new Date().toLocaleDateString('es-ES', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

// ── Avatar iniciales ──────────────────────────────────────────────────────────
const iniciales = computed(() => {
    if (!auth.usuario) return '?';
    return (auth.usuario.nombre?.[0] || '') + (auth.usuario.apellido?.[0] || '');
});

// ── Logout ────────────────────────────────────────────────────────────────────
const cerrandoSesion = ref(false);
async function handleLogout() {
    cerrandoSesion.value = true;
    await auth.logout();
    router.replace({ name: 'login' });
    cerrandoSesion.value = false;
}
</script>
