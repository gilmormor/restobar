<template>
  <Teleport to="body">
    <!-- Barra delgada en la parte superior — visible en toda petición axios -->
    <div
      v-show="visible"
      class="fixed top-0 left-0 right-0 z-[9999] pointer-events-none"
    >
      <!-- Barra principal -->
      <div
        class="h-[3px] transition-all ease-out"
        :class="completed ? 'duration-150 opacity-0' : 'duration-300 opacity-100'"
        :style="{ width: width + '%', background: 'linear-gradient(90deg, #f97316, #fb923c, #fbbf24)' }"
      />
      <!-- Brillo en el extremo derecho -->
      <div
        class="absolute top-0 h-[3px] w-20 rounded-full blur-[6px] opacity-70 transition-all duration-300"
        :style="{ left: `calc(${width}% - 5rem)`, background: '#fb923c' }"
      />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { loadingState } from '../../composables/useLoading.js';

const width     = ref(0);
const visible   = ref(false);
const completed = ref(false);
let   timer     = null;

// Simula progreso realista: rápido al inicio, se ralentiza esperando al servidor
function startProgress() {
  width.value   = 0;
  visible.value = true;
  completed.value = false;

  const increments = [15, 10, 8, 5, 4, 3, 2, 1]; // cada tick avanza menos
  let step = 0;

  function tick() {
    if (width.value >= 85) return; // no llega al 100 solo (espera la respuesta real)
    const inc = increments[Math.min(step, increments.length - 1)] * (0.7 + Math.random() * 0.6);
    width.value = Math.min(85, width.value + inc);
    step++;
    timer = setTimeout(tick, 250 + step * 50);
  }
  tick();
}

function finishProgress() {
  clearTimeout(timer);
  width.value = 100;
  completed.value = true;
  // Ocultar después de la animación de fade-out
  setTimeout(() => {
    visible.value   = false;
    width.value     = 0;
    completed.value = false;
  }, 350);
}

watch(
  () => loadingState.isLoading.value,
  (loading) => {
    if (loading) {
      startProgress();
    } else {
      finishProgress();
    }
  }
);
</script>
