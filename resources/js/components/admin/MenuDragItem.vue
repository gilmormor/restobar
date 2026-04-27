<template>
  <div class="menu-drag-item">

    <!-- ── Fila del ítem ─────────────────────────────────────────────────────── -->
    <div
      class="group flex items-center gap-2 py-2 px-3 rounded-lg
             hover:bg-orange-50 hover:border-orange-100
             border border-transparent transition-all select-none"
    >
      <!-- Asa de arrastre -->
      <span
        class="drag-handle flex-shrink-0 cursor-grab active:cursor-grabbing
               text-gray-300 hover:text-gray-500 p-0.5 rounded hover:bg-gray-100
               transition-colors"
        title="Arrastrar para mover"
      >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
          <circle cx="9"  cy="5"  r="1.5"/>
          <circle cx="15" cy="5"  r="1.5"/>
          <circle cx="9"  cy="12" r="1.5"/>
          <circle cx="15" cy="12" r="1.5"/>
          <circle cx="9"  cy="19" r="1.5"/>
          <circle cx="15" cy="19" r="1.5"/>
        </svg>
      </span>

      <!-- Ícono -->
      <span class="flex-shrink-0 w-5 text-center text-base leading-none">
        {{ resolveIcon(item.icono) }}
      </span>

      <!-- Nombre -->
      <span
        class="flex-1 text-sm truncate"
        :class="!item.ruta ? 'font-bold text-gray-800' : 'text-gray-700'"
      >
        {{ item.nombre }}
      </span>

      <!-- URL / Ruta -->
      <span class="hidden sm:block text-xs text-gray-400 font-mono flex-shrink-0">
        Url: {{ item.ruta || '#' }}
      </span>

      <!-- Acciones (aparecen al hover) -->
      <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
        <button
          @click.stop="$emit('edit', item)"
          class="w-6 h-6 inline-flex items-center justify-center rounded
                 text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
          title="Editar"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                     15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>
        <button
          @click.stop="$emit('delete', item)"
          class="w-6 h-6 inline-flex items-center justify-center rounded
                 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
          title="Eliminar"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2
                     0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- ── Zona de hijos (siempre existe para poder soltar dentro) ─────────────── -->
    <div class="pl-7">
      <VueDraggable
        v-model="children"
        :group="{ name: 'menus', pull: true, put: true }"
        handle=".drag-handle"
        :animation="200"
        ghost-class="drag-ghost"
        chosen-class="drag-chosen"
        :fallback-on-body="true"
        class="min-h-[4px]"
        @end="$emit('reorder')"
      >
        <MenuDragItem
          v-for="hijo in children"
          :key="hijo.id"
          :item="hijo"
          @reorder="$emit('reorder')"
          @edit="$emit('edit', $event)"
          @delete="$emit('delete', $event)"
        />
      </VueDraggable>

      <!-- Placeholder visible cuando no tiene hijos (zona de drop) -->
      <div
        v-if="children.length === 0"
        class="h-5 rounded border border-dashed border-gray-200 mx-1 mb-1"
      />
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { VueDraggable } from 'vue-draggable-plus';

// Necesario para que el componente pueda referenciarse a sí mismo
defineOptions({ name: 'MenuDragItem' });

const props = defineProps({
  item: { type: Object, required: true },
});

defineEmits(['reorder', 'edit', 'delete']);

// ── Mapa ícono Heroicon → emoji ────────────────────────────────────────────────
const iconMap = {
  HomeIcon: '📊', TableCellsIcon: '🪑', ClipboardListIcon: '📋',
  BookOpenIcon: '🍕', FireIcon: '🔥', CurrencyDollarIcon: '💰',
  ArchiveBoxIcon: '📦', UsersIcon: '👥', ChartBarIcon: '📈',
  Cog6ToothIcon: '⚙️', ShieldCheckIcon: '🛡️', UserCircleIcon: '👤',
  Bars3Icon: '☰', KeyIcon: '🔑', LockClosedIcon: '🔒',
};
function resolveIcon(ic) { return ic ? (iconMap[ic] ?? ic) : ''; }

// ── computed con setter para evitar mutación directa de prop ───────────────────
// El setter usa splice (mutación in-place del array reactivo del padre, no reasignación)
const children = computed({
  get: () => props.item.hijos ?? [],
  set: (newVal) => {
    if (Array.isArray(props.item.hijos)) {
      props.item.hijos.splice(0, props.item.hijos.length, ...newVal);
    }
  },
});
</script>

<style scoped>
/* Ítem fantasma mientras se arrastra */
:global(.drag-ghost) {
  opacity: 0.4;
  background: #fed7aa !important;  /* orange-200 */
  border-radius: 8px;
}
/* Ítem seleccionado (al hacer clic y mantener) */
:global(.drag-chosen) {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border-radius: 8px;
}
</style>
