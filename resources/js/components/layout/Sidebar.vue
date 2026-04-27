<template>
  <aside
    class="hidden md:block min-h-screen flex-shrink-0 relative transition-all duration-300"
    :class="collapsed ? 'w-16' : 'w-64'"
    @mouseenter="onEnter"
    @mouseleave="onLeave"
  >
    <!-- Panel visual — se expande encima del contenido cuando está colapsado -->
    <div
      class="absolute inset-y-0 left-0 flex flex-col bg-gray-900 text-white
             transition-all duration-300 ease-in-out overflow-hidden"
      :class="isExpanded ? 'w-64 shadow-2xl' : 'w-16'"
      style="z-index: 100;"
    >
      <!-- Logo -->
      <div
        class="flex items-center h-16 border-b border-gray-700 flex-shrink-0"
        :class="isExpanded ? 'px-4 gap-3' : 'justify-center'"
      >
        <span class="text-2xl leading-none flex-shrink-0">🍽️</span>
        <div
          class="overflow-hidden whitespace-nowrap transition-all duration-300"
          :class="isExpanded ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'"
        >
          <p class="text-base font-bold text-white leading-tight">Restobar</p>
          <p class="text-xs text-gray-400">Sistema de gestión</p>
        </div>
      </div>

      <!-- Navegación -->
      <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto overflow-x-hidden">

        <!-- Skeleton mientras carga -->
        <template v-if="menuItems.length === 0">
          <div v-for="n in 6" :key="n"
               class="h-10 rounded-lg bg-gray-700 animate-pulse mx-1 mb-1" />
        </template>

        <template v-else>
          <SidebarItem
            v-for="item in menuItems"
            :key="item.slug"
            :item="item"
            :expanded="isExpanded"
            :depth="0"
          />
        </template>
      </nav>

      <!-- Footer -->
      <div
        class="border-t border-gray-700 transition-all duration-300 overflow-hidden"
        :class="isExpanded ? 'max-h-16 opacity-100 py-4 px-4' : 'max-h-0 opacity-0'"
      >
        <p class="text-xs text-gray-500 whitespace-nowrap">v1.0.0 — Laravel 13 + Vue 3</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/auth.js';
import SidebarItem from './SidebarItem.vue';

const props = defineProps({
  collapsed: { type: Boolean, default: false },
});

const auth     = useAuthStore();
const hovering = ref(false);

const isExpanded = computed(() => !props.collapsed || hovering.value);

function onEnter() { if (props.collapsed) hovering.value = true; }
function onLeave() { hovering.value = false; }

// ── Mapa de iconos Heroicon → emoji ──────────────────────────────────────────
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
  Bars3Icon:          '☰',
  KeyIcon:            '🔑',
  LockClosedIcon:     '🔒',
};

function resolveIcon(icono) {
  if (!icono) return null;
  return iconMap[icono] ?? icono;
}

function mapItem(item) {
  return {
    slug:      item.slug,
    label:     item.nombre,
    icon:      resolveIcon(item.icono),
    routeName: item.ruta ?? null,
    to:        item.ruta ? { name: item.ruta } : null,
    hijos:     (item.hijos ?? []).map(h => ({
      slug:      h.slug,
      label:     h.nombre,
      icon:      resolveIcon(h.icono),
      routeName: h.ruta ?? null,
      to:        h.ruta ? { name: h.ruta } : null,
      hijos:     (h.hijos ?? []).map(n => ({
        slug:      n.slug,
        label:     n.nombre,
        icon:      resolveIcon(n.icono),
        routeName: n.ruta ?? null,
        to:        n.ruta ? { name: n.ruta } : null,
        hijos:     [],
      })),
    })),
  };
}

const menuItems = computed(() => auth.menuItems.map(mapItem));
</script>
