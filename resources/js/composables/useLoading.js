/**
 * Estado global de carga — singleton reactivo compartido entre todos los componentes.
 * Los interceptores de axios lo incrementan/decrementan automáticamente.
 */
import { reactive, computed } from 'vue';

const state = reactive({ count: 0 });

export const loadingState = {
  isLoading: computed(() => state.count > 0),
  start()   { state.count++; },
  stop()    { if (state.count > 0) state.count--; },
};
