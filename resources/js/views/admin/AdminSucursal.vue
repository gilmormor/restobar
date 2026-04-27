<template>
  <div>
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Sucursales</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de sucursales del negocio</p>
      </div>
      <button @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva sucursal
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="busqueda" type="text"
               placeholder="Buscar por nombre o dirección..."
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
        <div class="text-4xl mb-2">🏪</div>
        <p class="text-sm">{{ busqueda ? 'Sin resultados' : 'No hay sucursales registradas' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">Nombre</th>
              <th class="px-4 py-3 text-left">Dirección</th>
              <th class="px-4 py-3 text-left">Teléfono</th>
              <th class="px-4 py-3 text-left">Ubicación</th>
              <th class="px-4 py-3 text-center">Estado</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="s in filtradas" :key="s.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3">
                <div class="font-semibold text-gray-800">{{ s.nombre }}</div>
                <div v-if="s.abrev" class="text-xs text-gray-400 font-mono">{{ s.abrev }}</div>
              </td>
              <td class="px-4 py-3 text-gray-500 max-w-[160px] truncate">{{ s.direccion || '—' }}</td>
              <td class="px-4 py-3 text-gray-500">{{ s.telefonos || s.telefono || '—' }}</td>
              <td class="px-4 py-3 text-gray-500 text-xs">
                <span v-if="s.region">{{ s.region.nombre }}</span>
                <span v-if="s.provincia"> › {{ s.provincia.nombre }}</span>
                <span v-if="s.comuna"> › {{ s.comuna.nombre }}</span>
                <span v-if="!s.region">—</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="s.activa ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                      class="px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ s.activa ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                  <button @click="abrirEditar(s)"
                    class="w-7 h-7 flex items-center justify-center rounded
                           text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                    title="Editar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="eliminar(s)"
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
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar sucursal' : 'Nueva sucursal' }}
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

              <!-- Nombre y abreviatura -->
              <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.nombre" type="text" required autofocus
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Abrev.
                  </label>
                  <input v-model="form.abrev" type="text" maxlength="20"
                         placeholder="STGO"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>

              <!-- Dirección y teléfonos -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input v-model="form.direccion" type="text"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Teléfonos</label>
                  <input v-model="form.telefonos" type="text"
                         placeholder="+56 2 1234 5678, +56 9 8765 4321"
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

              <!-- Geografía encadenada -->
              <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ubicación geográfica</p>

                <!-- País -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                  <select v-model="form.pais_id" @change="onPaisChange"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white
                                 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option :value="null">— Seleccionar país —</option>
                    <option v-for="p in paises" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                  </select>
                </div>

                <!-- Región -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Región / Departamento / Estado
                  </label>
                  <select v-model="form.region_id" @change="onRegionChange"
                          :disabled="!form.pais_id || cargandoRegiones"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white
                                 focus:outline-none focus:ring-2 focus:ring-orange-400
                                 disabled:bg-gray-100 disabled:text-gray-400">
                    <option :value="null">
                      {{ cargandoRegiones ? 'Cargando…' : '— Seleccionar región —' }}
                    </option>
                    <option v-for="r in regiones" :key="r.id" :value="r.id">{{ r.nombre }}</option>
                  </select>
                </div>

                <!-- Provincia -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Provincia / Municipio / Ciudad
                  </label>
                  <select v-model="form.provincia_id" @change="onProvinciaChange"
                          :disabled="!form.region_id || cargandoProvincias"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white
                                 focus:outline-none focus:ring-2 focus:ring-orange-400
                                 disabled:bg-gray-100 disabled:text-gray-400">
                    <option :value="null">
                      {{ cargandoProvincias ? 'Cargando…' : '— Seleccionar provincia —' }}
                    </option>
                    <option v-for="p in provincias" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                  </select>
                </div>

                <!-- Comuna -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Comuna / Localidad / Parroquia
                  </label>
                  <select v-model="form.comuna_id"
                          :disabled="!form.provincia_id || cargandoComunas"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white
                                 focus:outline-none focus:ring-2 focus:ring-orange-400
                                 disabled:bg-gray-100 disabled:text-gray-400">
                    <option :value="null">
                      {{ cargandoComunas ? 'Cargando…' : '— Seleccionar comuna —' }}
                    </option>
                    <option v-for="c in comunas" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                  </select>
                </div>
              </div>

              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="form.activa" type="checkbox"
                       class="w-4 h-4 rounded accent-orange-500"/>
                Sucursal activa
              </label>

              <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-200
                                    rounded-lg px-3 py-2">{{ error }}</p>

              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando…' : (modoEdicion ? 'Actualizar' : 'Crear sucursal') }}
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

const sucursales        = ref([]);
const paises            = ref([]);
const regiones          = ref([]);
const provincias        = ref([]);
const comunas           = ref([]);
const cargando          = ref(false);
const cargandoRegiones  = ref(false);
const cargandoProvincias= ref(false);
const cargandoComunas   = ref(false);
const busqueda          = ref('');
const modalAbierto      = ref(false);
const modoEdicion       = ref(false);
const guardando         = ref(false);
const error             = ref('');

const formVacio = () => ({
  id: null, nombre: '', abrev: '', direccion: '', telefono: '',
  telefonos: '', email: '', logo: null, activa: true,
  pais_id: null, region_id: null, provincia_id: null, comuna_id: null,
});

const form = ref(formVacio());

const filtradas = computed(() => {
  const q = busqueda.value.toLowerCase();
  return q
    ? sucursales.value.filter(s =>
        s.nombre.toLowerCase().includes(q) ||
        (s.direccion ?? '').toLowerCase().includes(q)
      )
    : sucursales.value;
});

// ── Carga encadenada ──────────────────────────────────────────────────────────
async function onPaisChange() {
  form.value.region_id   = null;
  form.value.provincia_id= null;
  form.value.comuna_id   = null;
  regiones.value   = [];
  provincias.value = [];
  comunas.value    = [];
  if (!form.value.pais_id) return;
  cargandoRegiones.value = true;
  try {
    const { data } = await axios.get('/api/geo/regiones', { params: { pais_id: form.value.pais_id } });
    regiones.value = data;
  } finally {
    cargandoRegiones.value = false;
  }
}

async function onRegionChange() {
  form.value.provincia_id = null;
  form.value.comuna_id    = null;
  provincias.value = [];
  comunas.value    = [];
  if (!form.value.region_id) return;
  cargandoProvincias.value = true;
  try {
    const { data } = await axios.get('/api/geo/provincias', { params: { region_id: form.value.region_id } });
    provincias.value = data;
  } finally {
    cargandoProvincias.value = false;
  }
}

async function onProvinciaChange() {
  form.value.comuna_id = null;
  comunas.value        = [];
  if (!form.value.provincia_id) return;
  cargandoComunas.value = true;
  try {
    const { data } = await axios.get('/api/geo/comunas', { params: { provincia_id: form.value.provincia_id } });
    comunas.value = data;
  } finally {
    cargandoComunas.value = false;
  }
}

// ── Carga de datos base ───────────────────────────────────────────────────────
async function cargar() {
  cargando.value = true;
  try {
    const [rSuc, rPaises] = await Promise.all([
      axios.get('/api/sucursales'),
      axios.get('/api/geo/paises'),
    ]);
    sucursales.value = rSuc.data;
    paises.value     = rPaises.data;
  } finally {
    cargando.value = false;
  }
}

function abrirCrear() {
  form.value         = formVacio();
  regiones.value     = [];
  provincias.value   = [];
  comunas.value      = [];
  modoEdicion.value  = false;
  modalAbierto.value = true;
  error.value        = '';
}

async function abrirEditar(s) {
  form.value = {
    id:           s.id,
    nombre:       s.nombre,
    abrev:        s.abrev        ?? '',
    direccion:    s.direccion    ?? '',
    telefono:     s.telefono     ?? '',
    telefonos:    s.telefonos    ?? '',
    email:        s.email        ?? '',
    logo:         s.logo         ?? null,
    activa:       s.activa,
    pais_id:      null,
    region_id:    s.region_id    ?? null,
    provincia_id: s.provincia_id ?? null,
    comuna_id:    s.comuna_id    ?? null,
  };

  // Reconstruir selects encadenados si tiene región
  regiones.value   = [];
  provincias.value = [];
  comunas.value    = [];

  if (s.region_id) {
    // Buscar pais_id desde la región
    try {
      const { data } = await axios.get('/api/geo/regiones');
      const reg = data.find(r => r.id === s.region_id);
      if (reg) {
        form.value.pais_id = reg.pais_id;
        const [rReg, rProv] = await Promise.all([
          axios.get('/api/geo/regiones',  { params: { pais_id:   reg.pais_id } }),
          axios.get('/api/geo/provincias',{ params: { region_id: s.region_id } }),
        ]);
        regiones.value   = rReg.data;
        provincias.value = rProv.data;
      }
    } catch {}
  }

  if (s.provincia_id) {
    try {
      const { data } = await axios.get('/api/geo/comunas', { params: { provincia_id: s.provincia_id } });
      comunas.value = data;
    } catch {}
  }

  modoEdicion.value  = true;
  modalAbierto.value = true;
  error.value        = '';
}

async function guardar() {
  guardando.value = true;
  error.value     = '';
  try {
    const payload = {
      nombre:       form.value.nombre,
      abrev:        form.value.abrev        || null,
      direccion:    form.value.direccion    || null,
      telefono:     form.value.telefono     || null,
      telefonos:    form.value.telefonos    || null,
      email:        form.value.email        || null,
      activa:       form.value.activa,
      region_id:    form.value.region_id,
      provincia_id: form.value.provincia_id,
      comuna_id:    form.value.comuna_id,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/sucursales/${form.value.id}`, payload);
    } else {
      await axios.post('/api/sucursales', payload);
    }
    modalAbierto.value = false;
    await cargar();
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

async function eliminar(s) {
  if (!confirm(`¿Eliminar la sucursal "${s.nombre}"?`)) return;
  try {
    await axios.delete(`/api/sucursales/${s.id}`);
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
