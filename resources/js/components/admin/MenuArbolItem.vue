<template>
  <li class="border border-gray-200 rounded-lg bg-white mb-1">
    <!-- Cabecera del ítem -->
    <div
      class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 group"
      :style="{ paddingLeft: (depth * 18 + 12) + 'px' }"
    >
      <!-- Indicador de nivel -->
      <span v-if="depth > 0" class="text-gray-300 text-xs select-none">↳</span>

      <!-- Ícono (si tiene) -->
      <span v-if="item.icono" class="text-base w-6 text-center flex-shrink-0">
        {{ resolveIcon(item.icono) }}
      </span>
      <span v-else class="w-6 flex-shrink-0 flex items-center justify-center">
        <span class="w-2 h-2 rounded-full bg-gray-300" />
      </span>

      <!-- Nombre -->
      <span
        class="text-sm flex-1 min-w-0"
        :class="!item.ruta ? 'font-bold text-gray-800' : 'font-medium text-gray-700'"
      >
        {{ item.nombre }}
      </span>

      <!-- URL / ruta -->
      <span class="text-xs text-gray-400 font-mono hidden sm:inline flex-shrink-0">
        Url: <span :class="!item.ruta ? 'text-orange-400 font-semibold' : 'text-blue-400'">
          {{ item.ruta || '#' }}
        </span>
      </span>

      <!-- Slug -->
      <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono hidden md:inline flex-shrink-0">
        {{ item.slug }}
      </span>

      <!-- Acciones -->
      <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          @click="$emit('editar', item)"
          class="w-7 h-7 flex items-center justify-center rounded text-blue-500
                 hover:bg-blue-50 hover:text-blue-700 transition-colors"
          title="Editar"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                     m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>
        <button
          @click="$emit('eliminar', item)"
          class="w-7 h-7 flex items-center justify-center rounded text-red-400
                 hover:bg-red-50 hover:text-red-600 transition-colors"
          title="Eliminar"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                     m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Hijos (recursivo) -->
    <ul v-if="item.hijos && item.hijos.length" class="pl-2 space-y-1 pb-1">
      <MenuArbolItem
        v-for="hijo in item.hijos"
        :key="hijo.id"
        :item="hijo"
        :depth="depth + 1"
        @editar="$emit('editar', $event)"
        @eliminar="$emit('eliminar', $event)"
      />
    </ul>
  </li>
</template>

<script setup>
defineProps({
  item:  { type: Object, required: true },
  depth: { type: Number, default: 0 },
});

defineEmits(['editar', 'eliminar']);

const iconMap = {
  HomeIcon: '📊', TableCellsIcon: '🪑', ClipboardListIcon: '📋',
  BookOpenIcon: '🍕', FireIcon: '🔥', CurrencyDollarIcon: '💰',
  ArchiveBoxIcon: '📦', UsersIcon: '👥', ChartBarIcon: '📈',
  Cog6ToothIcon: '⚙️', ShieldCheckIcon: '🛡️', UserCircleIcon: '👤',
  Bars3Icon: '☰', KeyIcon: '🔑', LockClosedIcon: '🔒',
};

function resolveIcon(ic) {
  return iconMap[ic] ?? ic;
}
</script>
