<template>
  <div>
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Empresas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de empresas y razones sociales</p>
      </div>
      <button @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva empresa
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="busqueda" type="text"
               placeholder="Buscar por nombre, nombre comercial o ID fiscal..."
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
        <div class="text-4xl mb-2">🏢</div>
        <p class="text-sm">{{ busqueda ? 'Sin resultados' : 'No hay empresas registradas' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">Razón Social</th>
              <th class="px-4 py-3 text-left">Nombre Comercial</th>
              <th class="px-4 py-3 text-left">ID Fiscal</th>
              <th class="px-4 py-3 text-left">Giro</th>
              <th class="px-4 py-3 text-left">Moneda</th>
              <th class="px-4 py-3 text-center">Estado</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="e in filtradas" :key="e.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-semibold text-gray-800">{{ e.nombre }}</td>
              <td class="px-4 py-3 text-gray-600">{{ e.nombre_comercial || '—' }}</td>
              <td class="px-4 py-3">
                <span v-if="e.id_fiscal" class="font-mono text-xs text-gray-600">
                  <span class="text-gray-400 mr-1">{{ e.tipo_id_fiscal }}</span>{{ e.id_fiscal }}
                </span>
                <span v-else class="text-gray-300">—</span>
              </td>
              <td class="px-4 py-3 text-gray-500 max-w-[160px] truncate">{{ e.giro || '—' }}</td>
              <td class="px-4 py-3">
                <span v-if="e.moneda"
                      class="px-2 py-0.5 rounded-full text-xs font-mono font-medium bg-blue-50 text-blue-600">
                  {{ e.moneda.codigo }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="e.activa ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                      class="px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ e.activa ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                  <button @click="abrirEditar(e)"
                    class="w-7 h-7 flex items-center justify-center rounded
                           text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                    title="Editar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="eliminar(e)"
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
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar empresa' : 'Nueva empresa' }}
              </h3>
              <button @click="modalAbierto = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                       text-gray-400 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="guardar" class="px-6 py-5 space-y-4 overflow-y-auto">

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Razón Social <span class="text-red-500">*</span>
                </label>
                <input v-model="form.nombre" type="text" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Nombre Comercial
                  <span class="text-xs font-normal text-gray-400 ml-1">(Nombre de fantasía)</span>
                </label>
                <input v-model="form.nombre_comercial" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Tipo ID Fiscal</label>
                  <select v-model="form.tipo_id_fiscal"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">— Seleccionar —</option>
                    <option v-for="p in paises" :key="p.codigo_iso2"
                            :value="p.tipo_id_fiscal">
                      {{ p.tipo_id_fiscal }} ({{ p.nombre }})
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Número ID Fiscal
                    <span class="text-xs text-gray-400">(RUT / NIT / RIF)</span>
                  </label>
                  <input v-model="form.id_fiscal" type="text"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giro / Actividad Económica</label>
                <input v-model="form.giro" type="text"
                       placeholder="Ej: Servicios de Restaurante y Bar"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input v-model="form.direccion" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                  <input v-model="form.telefono" type="text"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input v-model="form.email" type="email"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                  <select v-model="form.pais_id"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option :value="null">— Seleccionar —</option>
                    <option v-for="p in paises" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Moneda Principal</label>
                  <select v-model="form.moneda_id"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option :value="null">— Seleccionar —</option>
                    <option v-for="m in monedas" :key="m.id" :value="m.id">
                      {{ m.codigo }} — {{ m.nombre }}
                    </option>
                  </select>
                </div>
              </div>

              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="form.activa" type="checkbox"
                       class="w-4 h-4 rounded accent-orange-500"/>
                Empresa activa
              </label>

              <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-200
                                    rounded-lg px-3 py-2">{{ error }}</p>

              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando…' : (modoEdicion ? 'Actualizar' : 'Crear empresa') }}
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

const empresas     = ref([]);
const paises       = ref([]);
const monedas      = ref([]);
const cargando     = ref(false);
const busqueda     = ref('');
const modalAbierto = ref(false);
const modoEdicion  = ref(false);
const guardando    = ref(false);
const error        = ref('');

const form = ref({
  id: null, nombre: '', nombre_comercial: '', tipo_id_fiscal: '',
  id_fiscal: '', giro: '', direccion: '', telefono: '', email: '',
  moneda_id: null, pais_id: null, activa: true,
});

const filtradas = computed(() => {
  const q = busqueda.value.toLowerCase();
  return q
    ? empresas.value.filter(e =>
        e.nombre.toLowerCase().includes(q) ||
        (e.nombre_comercial ?? '').toLowerCase().includes(q) ||
        (e.id_fiscal ?? '').toLowerCase().includes(q)
      )
    : empresas.value;
});

async function cargar() {
  cargando.value = true;
  try {
    const [rEmpresas, rPaises, rMonedas] = await Promise.all([
      axios.get('/api/empresas'),
      axios.get('/api/geo/paises'),
      axios.get('/api/monedas'),
    ]);
    empresas.value = rEmpresas.data;
    paises.value   = rPaises.data;
    monedas.value  = rMonedas.data;
  } finally {
    cargando.value = false;
  }
}

function abrirCrear() {
  form.value = {
    id: null, nombre: '', nombre_comercial: '', tipo_id_fiscal: '',
    id_fiscal: '', giro: '', direccion: '', telefono: '', email: '',
    moneda_id: null, pais_id: null, activa: true,
  };
  modoEdicion.value  = false;
  modalAbierto.value = true;
  error.value        = '';
}

function abrirEditar(e) {
  form.value = {
    id:               e.id,
    nombre:           e.nombre,
    nombre_comercial: e.nombre_comercial ?? '',
    tipo_id_fiscal:   e.tipo_id_fiscal ?? '',
    id_fiscal:        e.id_fiscal ?? '',
    giro:             e.giro ?? '',
    direccion:        e.direccion ?? '',
    telefono:         e.telefono ?? '',
    email:            e.email ?? '',
    moneda_id:        e.moneda_id ?? null,
    pais_id:          e.pais_id ?? null,
    activa:           e.activa,
  };
  modoEdicion.value  = true;
  modalAbierto.value = true;
  error.value        = '';
}

async function guardar() {
  guardando.value = true;
  error.value     = '';
  try {
    const payload = {
      nombre:           form.value.nombre,
      nombre_comercial: form.value.nombre_comercial || null,
      tipo_id_fiscal:   form.value.tipo_id_fiscal   || null,
      id_fiscal:        form.value.id_fiscal         || null,
      giro:             form.value.giro              || null,
      direccion:        form.value.direccion         || null,
      telefono:         form.value.telefono          || null,
      email:            form.value.email             || null,
      moneda_id:        form.value.moneda_id,
      pais_id:          form.value.pais_id,
      activa:           form.value.activa,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/empresas/${form.value.id}`, payload);
    } else {
      await axios.post('/api/empresas', payload);
    }
    modalAbierto.value = false;
    await cargar();
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

async function eliminar(e) {
  if (!confirm(`¿Eliminar la empresa "${e.nombre}"?`)) return;
  try {
    await axios.delete(`/api/empresas/${e.id}`);
    await cargar();
  } catch (err) {
    alert(err.response?.data?.message ?? 'No se pudo eliminar');
  }
}

onMounted(cargar);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
