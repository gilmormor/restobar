<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-orange-900
              flex items-center justify-center p-4">

    <!-- Card -->
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden">

      <!-- Header naranja -->
      <div class="bg-orange-500 px-8 py-8 text-center">
        <div class="text-5xl mb-2">🍽️</div>
        <h1 class="text-2xl font-bold text-white tracking-wide">Restobar</h1>
        <p class="text-orange-100 text-sm mt-1">Sistema de Gestión</p>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="handleLogin" class="px-8 py-8 space-y-5">

        <!-- Error global -->
        <div v-if="error"
             class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm flex items-start gap-2">
          <span class="text-lg leading-none mt-0.5">⚠️</span>
          <span>{{ error }}</span>
        </div>

        <!-- Usuario -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">👤</span>
            <input
              v-model="form.usuario"
              type="text"
              autocomplete="username"
              placeholder="Ingresa tu usuario"
              class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg
                     focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                     text-gray-800 placeholder-gray-400 transition"
              :disabled="cargando"
              @keydown.enter.prevent="$refs.inputPassword.focus()"
            />
          </div>
        </div>

        <!-- Contraseña -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">🔒</span>
            <input
              ref="inputPassword"
              v-model="form.password"
              :type="mostrarPassword ? 'text' : 'password'"
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full pl-10 pr-11 py-2.5 border border-gray-300 rounded-lg
                     focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent
                     text-gray-800 placeholder-gray-400 transition"
              :disabled="cargando"
            />
            <button
              type="button"
              @click="mostrarPassword = !mostrarPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600
                     text-lg focus:outline-none"
              tabindex="-1"
            >
              {{ mostrarPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <!-- Botón -->
        <button
          type="submit"
          :disabled="cargando || !form.usuario || !form.password"
          class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                 text-white font-semibold py-3 rounded-lg transition-all duration-200
                 flex items-center justify-center gap-2 text-base
                 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2"
        >
          <svg v-if="cargando" class="animate-spin h-5 w-5 text-white"
               xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <span>{{ cargando ? 'Ingresando...' : 'Ingresar' }}</span>
        </button>

      </form>

      <!-- Footer -->
      <div class="px-8 pb-6 text-center text-xs text-gray-400">
        v1.0.0 &mdash; Restobar Sistema
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const router   = useRouter();
const authStore = useAuthStore();

const form = reactive({ usuario: '', password: '' });
const error          = ref('');
const cargando       = ref(false);
const mostrarPassword = ref(false);

async function handleLogin() {
    if (!form.usuario || !form.password) return;

    error.value   = '';
    cargando.value = true;

    try {
        await authStore.login({ usuario: form.usuario, password: form.password });
        router.replace({ name: 'dashboard' });
    } catch (e) {
        const msg = e.response?.data?.message;
        if (e.response?.status === 401) {
            error.value = 'Usuario o contraseña incorrectos.';
        } else if (e.response?.status === 422) {
            const errores = e.response.data?.errors;
            error.value = errores
                ? Object.values(errores).flat().join(' ')
                : (msg || 'Error de validación.');
        } else {
            error.value = msg || 'Error al conectar con el servidor.';
        }
    } finally {
        cargando.value = false;
    }
}
</script>
