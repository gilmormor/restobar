<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Roles</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de roles del sistema</p>
      </div>
      <button
        @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo rol
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

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
        Cargando roles...
      </div>

      <!-- Vacío -->
      <div v-else-if="!errorMsg && roles.length === 0"
           class="py-12 text-center text-gray-400">
        <div class="text-4xl mb-2">🔑</div>
        <p class="text-sm">Sin roles registrados</p>
      </div>

      <!-- Tabla -->
      <div v-else-if="roles.length > 0" class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Nombre</th>
              <th class="px-4 py-3 text-left">Descripción</th>
              <th class="px-4 py-3 text-center">Superadmin</th>
              <th class="px-4 py-3 text-center">Estado</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="r in roles" :key="r.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 text-gray-400 text-xs">{{ r.id }}</td>
              <td class="px-4 py-3 font-semibold text-gray-800">{{ r.nombre }}</td>
              <td class="px-4 py-3 text-gray-500">{{ r.descripcion }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="r.es_superadmin"
                      class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                  Sí
                </span>
                <span v-else class="text-gray-300 text-xs">No</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="r.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'">
                  {{ r.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="abrirEditar(r)"
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
                {{ modoEdicion ? 'Editar rol' : 'Nuevo rol' }}
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input v-model="form.nombre" type="text" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <input v-model="form.descripcion" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div class="flex items-center gap-2">
                <input v-model="form.es_superadmin" type="checkbox" id="chkSuper"
                       class="w-4 h-4 accent-orange-500"/>
                <label for="chkSuper" class="text-sm text-gray-700">Es superadministrador</label>
              </div>
              <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="chkActivo"
                       class="w-4 h-4 accent-orange-500"/>
                <label for="chkActivo" class="text-sm text-gray-700">Activo</label>
              </div>
              <p v-if="errorForm" class="text-sm text-red-600 bg-red-50 border border-red-200
                                         rounded-lg px-3 py-2">{{ errorForm }}</p>
              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando...' : (modoEdicion ? 'Actualizar' : 'Crear rol') }}
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
import { ref, onMounted } from 'vue';
import axios from 'axios';

const roles     = ref([]);
const cargando  = ref(false);
const errorMsg  = ref('');
const errorForm = ref('');
const modalAbierto = ref(false);
const modoEdicion  = ref(false);
const guardando    = ref(false);

const form = ref({
  id: null, nombre: '', descripcion: '', es_superadmin: false, activo: true,
});

async function cargar() {
  cargando.value = true;
  errorMsg.value = '';
  try {
    const { data } = await axios.get('/api/roles');
    roles.value = Array.isArray(data) ? data : (data.data ?? []);
  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Error al cargar roles';
    console.error('AdminRol cargar:', e);
  } finally {
    cargando.value = false;
  }
}

function abrirCrear() {
  form.value = { id: null, nombre: '', descripcion: '', es_superadmin: false, activo: true };
  modoEdicion.value = false; modalAbierto.value = true; errorForm.value = '';
}

function abrirEditar(r) {
  form.value = {
    id: r.id, nombre: r.nombre, descripcion: r.descripcion ?? '',
    es_superadmin: !!r.es_superadmin, activo: !!r.activo,
  };
  modoEdicion.value = true; modalAbierto.value = true; errorForm.value = '';
}

async function guardar() {
  guardando.value = true; errorForm.value = '';
  try {
    const payload = {
      nombre: form.value.nombre, descripcion: form.value.descripcion,
      es_superadmin: form.value.es_superadmin, activo: form.value.activo,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/roles/${form.value.id}`, payload);
    } else {
      await axios.post('/api/roles', payload);
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
