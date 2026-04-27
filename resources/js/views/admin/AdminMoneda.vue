<template>
  <div>
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Monedas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de monedas y tasas de cambio</p>
      </div>
      <button @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva moneda
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="busqueda" type="text"
               placeholder="Buscar por nombre o código..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-orange-300"/>
      </div>

      <div v-if="cargando" class="py-12 text-center text-gray-400">
        <svg class="animate-spin w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Cargando...
      </div>

      <div v-else-if="filtradas.length === 0" class="py-12 text-center text-gray-400">
        <div class="text-4xl mb-2">💱</div>
        <p class="text-sm">{{ busqueda ? 'Sin resultados' : 'No hay monedas registradas' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">Código</th>
              <th class="px-4 py-3 text-left">Nombre</th>
              <th class="px-4 py-3 text-center">Símbolo</th>
              <th class="px-4 py-3 text-right">Valor</th>
              <th class="px-4 py-3 text-center">Local</th>
              <th class="px-4 py-3 text-center">Estado</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="m in filtradas" :key="m.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-mono font-bold text-orange-600">{{ m.codigo }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ m.nombre }}</td>
              <td class="px-4 py-3 text-center font-mono text-gray-600">{{ m.simbolo }}</td>
              <td class="px-4 py-3 text-right font-mono text-gray-600">{{ m.valor }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="m.es_local"
                      class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                  Local
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="m.activa
                  ? 'bg-green-100 text-green-700'
                  : 'bg-gray-100 text-gray-500'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ m.activa ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                  <button @click="abrirEditar(m)"
                    class="w-7 h-7 flex items-center justify-center rounded
                           text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                    title="Editar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="eliminar(m)"
                    class="w-7 h-7 flex items-center justify-center rounded
                           text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                    title="Eliminar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal crear/editar -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modalAbierto"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
             @click.self="modalAbierto = false">
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar moneda' : 'Nueva moneda' }}
              </h3>
              <button @click="modalAbierto = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                       text-gray-400 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="guardar" class="px-6 py-5 space-y-4">

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Código <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.codigo" type="text" required maxlength="10"
                         placeholder="CLP, USD, COP..."
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Símbolo <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.simbolo" type="text" required maxlength="10"
                         placeholder="$, €, Bs."
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Nombre <span class="text-red-500">*</span>
                </label>
                <input v-model="form.nombre" type="text" required
                       placeholder="Peso Chileno, Dólar Americano..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción
                </label>
                <input v-model="form.descripcion" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Valor vs. moneda local
                  <span class="text-xs font-normal text-gray-400 ml-1">(1 para moneda local)</span>
                </label>
                <input v-model.number="form.valor" type="number" step="0.0001" min="0" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                  <input v-model="form.es_local" type="checkbox"
                         class="w-4 h-4 rounded accent-orange-500"/>
                  Es moneda local
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                  <input v-model="form.activa" type="checkbox"
                         class="w-4 h-4 rounded accent-orange-500"/>
                  Activa
                </label>
              </div>

              <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-200
                                    rounded-lg px-3 py-2">{{ error }}</p>

              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando…' : (modoEdicion ? 'Actualizar' : 'Crear moneda') }}
                </button>
                <button type="button" @click="modalAbierto = false"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium
                               py-2.5 rounded-lg transition-colors text-sm">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const monedas      = ref([]);
const cargando     = ref(false);
const busqueda     = ref('');
const modalAbierto = ref(false);
const modoEdicion  = ref(false);
const guardando    = ref(false);
const error        = ref('');

const form = ref({
  id: null, codigo: '', simbolo: '', nombre: '', descripcion: '',
  valor: 1, es_local: false, activa: true,
});

const filtradas = computed(() => {
  const q = busqueda.value.toLowerCase();
  return q
    ? monedas.value.filter(m =>
        m.nombre.toLowerCase().includes(q) ||
        m.codigo.toLowerCase().includes(q)
      )
    : monedas.value;
});

async function cargar() {
  cargando.value = true;
  try {
    const { data } = await axios.get('/api/monedas');
    monedas.value = data;
  } finally {
    cargando.value = false;
  }
}

function abrirCrear() {
  form.value = { id: null, codigo: '', simbolo: '', nombre: '', descripcion: '', valor: 1, es_local: false, activa: true };
  modoEdicion.value  = false;
  modalAbierto.value = true;
  error.value        = '';
}

function abrirEditar(m) {
  form.value = { ...m };
  modoEdicion.value  = true;
  modalAbierto.value = true;
  error.value        = '';
}

async function guardar() {
  guardando.value = true;
  error.value     = '';
  try {
    const payload = {
      codigo: form.value.codigo, simbolo: form.value.simbolo,
      nombre: form.value.nombre, descripcion: form.value.descripcion || null,
      valor: form.value.valor, es_local: form.value.es_local,
      activa: form.value.activa,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/monedas/${form.value.id}`, payload);
    } else {
      await axios.post('/api/monedas', payload);
    }
    modalAbierto.value = false;
    await cargar();
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

async function eliminar(m) {
  if (!confirm(`¿Eliminar la moneda "${m.nombre}" (${m.codigo})?`)) return;
  try {
    await axios.delete(`/api/monedas/${m.id}`);
    await cargar();
  } catch (e) {
    alert(e.response?.data?.message ?? 'No se pudo eliminar');
  }
}

onMounted(cargar);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
