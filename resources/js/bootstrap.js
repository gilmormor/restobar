import axios from 'axios';
import { loadingState } from './composables/useLoading.js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Interceptores globales de axios
 * Cada petición incrementa el contador → aparece la barra superior automáticamente
 * Cada respuesta (éxito o error) lo decrementa → desaparece la barra
 * No hay que tocar nada en cada componente para que funcione.
 */
axios.interceptors.request.use(config => {
  loadingState.start();
  return config;
});

axios.interceptors.response.use(
  response => {
    loadingState.stop();
    return response;
  },
  error => {
    loadingState.stop();
    return Promise.reject(error);
  }
);
