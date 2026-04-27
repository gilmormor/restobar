<template>
  <div>
    <label
      class="flex items-center gap-2 py-1.5 px-3 rounded-lg hover:bg-gray-50 cursor-pointer"
      :style="{ paddingLeft: (depth * 20 + 12) + 'px' }"
    >
      <input
        type="checkbox"
        :checked="seleccionados.has(item.id)"
        @change="$emit('toggle', item.id)"
        class="w-4 h-4 accent-orange-500 rounded flex-shrink-0"
      />
      <span v-if="item.icono" class="text-sm flex-shrink-0">{{ item.icono }}</span>
      <span
        class="text-sm flex-1"
        :class="!item.ruta ? 'font-bold text-gray-700' : 'text-gray-600'"
      >
        {{ item.nombre }}
      </span>
      <span class="text-xs text-gray-400 font-mono ml-auto flex-shrink-0">
        {{ item.ruta || '#' }}
      </span>
    </label>

    <!-- Hijos recursivos -->
    <MenuCheckItem
      v-for="hijo in item.hijos"
      :key="hijo.id"
      :item="hijo"
      :seleccionados="seleccionados"
      :depth="depth + 1"
      @toggle="$emit('toggle', $event)"
    />
  </div>
</template>

<script setup>
defineProps({
  item:         { type: Object,  required: true },
  seleccionados:{ type: Object,  required: true },  // Set
  depth:        { type: Number,  default: 0 },
});

defineEmits(['toggle']);
</script>
