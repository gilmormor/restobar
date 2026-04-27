<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Permisos</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de permisos del sistema</p>
      </div>
      <button
        @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo permiso
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

      <div class="p-4 border-b border-gray-100">
        <input v-model="busqueda" type="text"
               placeholder="Buscar por slug o nombre..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-orange-300"/>
      </div>

      <!-- Error -->
      <div v-if="errorMsg"
           class="m-4 bg-red-50 border border-red-200 text-red-700 rounded-lg
                  px-4 py-3 text-sm flex items-center gap-2">
        <span>⚠️</span> {{ errorMsg }}
      </div>

      <!-- Loading -->
      <div v-if="cargando" class="py-12 text-center text-gray-400">
        <svg class="animate-spin w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Cargando permisos...
      </div>

      <!-- Vacío -->
      <div v-else-if="!errorMsg && permisosFiltrados.length === 0"
           class="py-12 text-center text-gray-400">
        <div class="text-4xl mb-2">🔒</div>
        <p class="text-sm">{{ busqueda ? 'Sin resultados para "' + busqueda + '"' : 'Sin permisos registrados' }}</p>
      </div>

      <!-- Tabla -->
      <div v-else-if="permisosFiltrados.length > 0" class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Slug</th>
              <th class="px-4 py-3 text-left">Nombre</th>
              <th class="px-4 py-3 text-left">Menú</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="p in permisosFiltrados" :key="p.id"
                class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 text-gray-400 text-xs">{{ p.id }}</td>
              <td class="px-4 py-3">
                <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">
                  {{ p.slug }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-700">{{ p.nombre }}</td>
              <td class="px-4 py-3 text-gray-400 text-xs">{{ p.menu?.nombre ?? '—' }}</td>
              <td class="px-4 py-3 text-center">
                <button @click="abrirEditar(p)"
                        class="w-7 h-7 inline-flex items-center justify-center rounded
                               text-blue-500 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                        title="Editar">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Contador -->
        <div class="px-4 py-2 border-t border-gray-100 text-xs text-gray-400">
          {{ permisosFiltrados.length }} permiso{{ permisosFiltrados.length !== 1 ? 's' : '' }}
          {{ busqueda ? 'encontrados' : 'en total' }}
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modalAbierto"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
             @click.self="modalAbierto = false">
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar permiso' : 'Nuevo permiso' }}
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
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Slug <span class="text-red-500">*</span>
                  <span class="text-xs font-normal text-gray-400 ml-1">ej: modulo.accion</span>
                </label>
                <input v-model="form.slug" type="text" required autofocus
                       placeholder="mesas.crear"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input v-model="form.nombre" type="text" required
                       placeholder="Crear mesa"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <input v-model="form.descripcion" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <p v-if="errorForm" class="text-sm text-red-600 bg-red-50 border border-red-200
                                         rounded-lg px-3 py-2">{{ errorForm }}</p>
              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando...' : (modoEdicion ? 'Actualizar' : 'Crear permiso') }}
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

const permisos  = ref([]);
const busqueda  = ref('');
const cargando  = ref(false);
const errorMsg  = ref('');
const errorForm = ref('');
const modalAbierto = ref(false);
const modoEdicion  = ref(false);
const guardando    = ref(false);

const form = ref({ id: null, slug: '', nombre: '', descripcion: '' });

const permisosFiltrados = computed(() => {
  const q = busqueda.value.toLowerCase();
  if (!q) return permisos.value;
  return permisos.value.filter(p =>
    p.slug?.toLowerCase().includes(q) || p.nombre?.toLowerCase().includes(q)
  );
});

async function cargar() {
  cargando.value = true;
  errorMsg.value = '';
  try {
    const { data } = await axios.get('/api/permisos');
    permisos.value = Array.isArray(data) ? data : (data.data ?? []);
  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Error al cargar permisos';
    console.error('AdminPermiso cargar:', e);
  } finally {
    cargando.value = false;
  }
}

function abrirCrear() {
  form.value = { id: null, slug: '', nombre: '', descripcion: '' };
  modoEdicion.value = false; modalAbierto.value = true; errorForm.value = '';
}

function abrirEditar(p) {
  form.value = { id: p.id, slug: p.slug, nombre: p.nombre, descripcion: p.descripcion ?? '' };
  modoEdicion.value = true; modalAbierto.value = true; errorForm.value = '';
}

async function guardar() {
  guardando.value = true; errorForm.value = '';
  try {
    const payload = {
      slug: form.value.slug, nombre: form.value.nombre,
      descripcion: form.value.descripcion || null,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/permisos/${form.value.id}`, payload);
    } else {
      await axios.post('/api/permisos', payload);
    }
    modalAbierto.value = false;
    await cargar();
  } catch (e) {
    const errs = e.response?.data?.errors;
    errorForm.value = errs
      ? Object.values(errs).flat().join(' ')
      : (e.response?.data?.message ?? 'Error al guardar');
  } finally {
    guardando.value = false;
  }
}

onMounted(cargar);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
