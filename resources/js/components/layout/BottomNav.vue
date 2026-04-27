<template>
  <!-- Barra inferior - solo visible en móvil -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex md:hidden z-40">
    <router-link
      v-for="item in mainItems"
      :key="item.slug"
      :to="item.to"
      class="flex-1 flex flex-col items-center justify-center py-2 gap-0.5 text-xs font-medium transition-colors duration-150"
      :class="$route.name === item.routeName ? 'text-orange-500' : 'text-gray-400'"
    >
      <span class="text-xl leading-none">{{ item.icon }}</span>
      <span class="leading-tight">{{ item.shortLabel }}</span>
    </router-link>

    <!-- Botón "Más" (solo si hay items adicionales) -->
    <button
      v-if="moreItems.length"
      @click="abrirMas"
      class="flex-1 flex flex-col items-center justify-center py-2 gap-0.5 text-xs font-medium transition-colors duration-150"
      :class="enSeccion ? 'text-orange-500' : 'text-gray-400'"
    >
      <span class="text-xl leading-none">⋯</span>
      <span class="leading-tight">Más</span>
    </button>
  </nav>

  <!-- Overlay oscuro -->
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="mostrarMas"
        class="fixed inset-0 bg-black/40 z-50 md:hidden"
        @click="mostrarMas = false"
      />
    </Transition>

    <!-- Sheet inferior "Más" -->
    <Transition name="slide-up">
      <div
        v-if="mostrarMas"
        class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 md:hidden shadow-2xl"
      >
        <!-- Handle de arrastre -->
        <div class="flex justify-center pt-3 pb-2">
          <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
        </div>

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 pb-3">Más opciones</p>

        <div class="grid grid-cols-4 gap-1 px-4 pb-6">
          <router-link
            v-for="item in moreItems"
            :key="item.slug"
            :to="item.to"
            @click="mostrarMas = false"
            class="flex flex-col items-center gap-1.5 p-3 rounded-2xl transition-colors duration-150"
            :class="$route.name === item.routeName
              ? 'bg-orange-50 text-orange-500'
              : 'text-gray-600 active:bg-gray-100'"
          >
            <span class="text-3xl leading-none">{{ item.icon }}</span>
            <span class="text-xs text-center leading-tight font-medium">{{ item.shortLabel }}</span>
          </router-link>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth.js';

const route      = useRoute();
const router     = useRouter();
const auth       = useAuthStore();
const mostrarMas = ref(false);

function routeValida(ruta) {
  if (!ruta || ruta === '#') return false;
  try {
    return router.resolve({ name: ruta }).matched.length > 0;
  } catch {
    return false;
  }
}

// ── Mapa de iconos ────────────────────────────────────────────────────────────
const iconMap = {
  HomeIcon:           '📊',
  TableCellsIcon:     '🪑',
  ClipboardListIcon:  '📋',
  BookOpenIcon:       '🍕',
  FireIcon:           '🔥',
  CurrencyDollarIcon: '💰',
  ArchiveBoxIcon:     '📦',
  UsersIcon:          '👥',
  ChartBarIcon:       '📈',
  Cog6ToothIcon:      '⚙️',
  ShieldCheckIcon:    '🛡️',
  UserCircleIcon:     '👤',
  KeyIcon:            '🔑',
  LockClosedIcon:     '🔒',
  Bars3Icon:          '☰',
};

// Etiquetas cortas para la barra inferior (espacio limitado)
const shortLabelMap = {
  dashboard:  'Inicio',
  mesas:      'Mesas',
  pedidos:    'Pedidos',
  menu:       'Carta',
  cocina:     'Cocina',
  caja:       'Caja',
  inventario: 'Inventario',
  empleados:  'Empleados',
  reportes:   'Reportes',
};

function resolveIcon(icono) {
  if (!icono) return '📄';
  return iconMap[icono] ?? icono;
}

// ── Aplana todos los niveles y devuelve solo ítems con ruta válida ────────────
function aplanarItems(items) {
  const result = [];
  for (const item of items) {
    if (item.ruta) {
      result.push(item);
    }
    if (item.hijos?.length) {
      result.push(...aplanarItems(item.hijos));
    }
  }
  return result;
}

// ── Items calculados desde el store ──────────────────────────────────────────
const allItems = computed(() =>
  aplanarItems(auth.menuItems)
    .filter(item => routeValida(item.ruta))
    .map(item => ({
      slug:       item.slug,
      label:      item.nombre,
      shortLabel: (() => {
        const l = shortLabelMap[item.ruta] ?? item.nombre ?? '';
        return l.length > 8 ? l.slice(0, 7) + '…' : l;
      })(),
      icon:       resolveIcon(item.icono),
      routeName:  item.ruta,
      to:         { name: item.ruta },
    }))
);

const mainItems = computed(() => allItems.value.slice(0, 4));
const moreItems = computed(() => allItems.value.slice(4));

const enSeccion = computed(() =>
  moreItems.value.some(item => item.routeName === route.name)
);

function abrirMas() {
  mostrarMas.value = true;
}
</script>

<style scoped>
/* Animación fade para el overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Animación slide-up para el sheet */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}
</style>
