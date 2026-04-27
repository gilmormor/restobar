<template>
  <button
    v-bind="$attrs"
    :disabled="loading || $attrs.disabled"
    :class="[
      'inline-flex items-center justify-center gap-2 font-medium transition-all duration-150 select-none',
      'focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-orange-400',
      loading ? 'cursor-wait opacity-80' : '',
      $attrs.disabled ? 'cursor-not-allowed opacity-50' : '',
    ]"
  >
    <!-- Spinner SVG moderno (anillo fino) -->
    <svg
      v-if="loading"
      class="animate-spin flex-shrink-0"
      :class="spinnerSize"
      viewBox="0 0 24 24"
      fill="none"
    >
      <!-- Pista (track) -->
      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25" />
      <!-- Arco giratorio -->
      <path
        d="M12 2 a10 10 0 0 1 10 10"
        stroke="currentColor"
        stroke-width="3"
        stroke-linecap="round"
      />
    </svg>

    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'sm',   // 'xs' | 'sm' | 'md'
  },
});

const spinnerSize = computed(() => ({
  xs: 'w-3 h-3',
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
}[props.size] ?? 'w-4 h-4'));
</script>
