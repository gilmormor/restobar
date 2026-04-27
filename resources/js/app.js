import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import { useAuthStore } from './stores/auth.js';

const app   = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Restaurar sesión del localStorage antes de montar
// (para que el guard del router ya tenga el estado correcto)
const auth = useAuthStore();
auth.inicializar();

app.mount('#app');
