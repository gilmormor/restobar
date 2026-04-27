<template>
  <!-- ── Ítem con ruta directa (hoja) ───────────────────────────────────────── -->
  <router-link
    v-if="item.to"
    :to="item.to"
    class="flex items-center h-10 rounded-lg text-sm font-medium
           transition-colors duration-150 w-full"
    :class="[
      itemPadding,
      isActive
        ? 'bg-orange-500 text-white'
        : 'text-gray-300 hover:bg-gray-700 hover:text-white',
    ]"
    :title="!expanded ? item.label : undefined"
  >
    <span v-if="item.icon" class="text-lg leading-none flex-shrink-0">{{ item.icon }}</span>
    <span v-else-if="depth > 0" class="w-4 flex-shrink-0 flex items-center justify-center">
      <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60" />
    </span>

    <span
      class="overflow-hidden whitespace-nowrap transition-all duration-300 ml-2"
      :class="expanded ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 ml-0'"
    >
      {{ item.label }}
    </span>
  </router-link>

  <!-- ── Grupo (sin ruta) con hijos colapsables ─────────────────────────────── -->
  <div v-else>
    <button
      @click="toggleOpen"
      class="flex items-center h-10 rounded-lg text-sm font-medium
             transition-colors duration-150 w-full text-left"
      :class="[
        itemPadding,
        isGroupActive
          ? 'text-orange-400'
          : 'text-gray-300 hover:bg-gray-700 hover:text-white',
      ]"
      :title="!expanded ? item.label : undefined"
    >
      <!-- Ícono o punto -->
      <span v-if="item.icon" class="text-lg leading-none flex-shrink-0">{{ item.icon }}</span>
      <span v-else-if="depth > 0" class="w-4 flex-shrink-0 flex items-center justify-center">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60" />
      </span>

      <!-- Label -->
      <span
        class="overflow-hidden whitespace-nowrap transition-all duration-300 ml-2 flex-1"
        :class="expanded ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0 ml-0'"
      >
        {{ item.label }}
      </span>

      <!-- Chevron -->
      <svg
        v-if="expanded && item.hijos?.length"
        class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200"
        :class="open ? 'rotate-90' : ''"
        fill="none" stroke="currentColor" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>

    <!-- Hijos (colapsable) -->
    <Transition
      enter-active-class="transition-all duration-200 ease-out overflow-hidden"
      enter-from-class="max-h-0 opacity-0"
      enter-to-class="max-h-96 opacity-100"
      leave-active-class="transition-all duration-150 ease-in overflow-hidden"
      leave-from-class="max-h-96 opacity-100"
      leave-to-class="max-h-0 opacity-0"
    >
      <div v-if="open && expanded && item.hijos?.length" class="mt-0.5 space-y-0.5">
        <SidebarItem
          v-for="hijo in item.hijos"
          :key="hijo.slug"
          :item="hijo"
          :expanded="expanded"
          :depth="depth + 1"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
  item:     { type: Object, required: true },
  expanded: { type: Boolean, default: true },
  depth:    { type: Number, default: 0 },
});

const route = useRoute();

// Padding según profundidad
const itemPadding = computed(() => {
  if (!props.expanded) return 'justify-center px-0';
  const left = ['px-3', 'pl-5 pr-3', 'pl-7 pr-3'];
  return left[Math.min(props.depth, 2)] + ' gap-0';
});

// ¿Está activo este ítem?
const isActive = computed(() =>
  props.item.routeName && route.name === props.item.routeName
);

// ¿Algún hijo está activo? (para resaltar el grupo)
function hayHijoActivo(item) {
  if (!item.hijos?.length) return false;
  return item.hijos.some(h => h.routeName === route.name || hayHijoActivo(h));
}
const isGroupActive = computed(() => hayHijoActivo(props.item));

// ── Abrir/cerrar grupo ────────────────────────────────────────────────────────
const open = ref(isGroupActive.value); // inicia abierto si hay un hijo activo

function toggleOpen() {
  open.value = !open.value;
}

// Abre automáticamente si un hijo está activo tras navegar
import { watch } from 'vue';
watch(isGroupActive, (val) => { if (val) open.value = true; });
</script>
