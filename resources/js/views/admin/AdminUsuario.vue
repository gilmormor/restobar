<template>
  <div>
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Usuarios</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestión de usuarios del sistema</p>
      </div>
      <button
        @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo usuario
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por usuario, nombre o email..."
          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                 focus:outline-none focus:ring-2 focus:ring-orange-300"
        />
      </div>

      <!-- Error -->
      <div v-if="errorMsg"
           class="mx-4 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-lg
                  px-4 py-3 text-sm flex items-center gap-2">
        <span>⚠️</span> {{ errorMsg }}
      </div>

      <!-- Loading -->
      <div v-if="cargando" class="py-12 text-center text-gray-400">
        <svg class="animate-spin w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Cargando usuarios...
      </div>

      <!-- Vacío -->
      <div v-else-if="!errorMsg && usuariosFiltrados.length === 0"
           class="py-12 text-center text-gray-400">
        <div class="text-4xl mb-2">👤</div>
        <p class="text-sm">{{ busqueda ? 'Sin resultados para "' + busqueda + '"' : 'Sin usuarios registrados' }}</p>
      </div>

      <!-- Datos -->
      <div v-else-if="usuariosFiltrados.length > 0" class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Usuario</th>
              <th class="px-4 py-3 text-left">Nombre</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-center">Roles</th>
              <th class="px-4 py-3 text-center">Estado</th>
              <th class="px-4 py-3 text-center w-20">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="u in usuariosFiltrados" :key="u.id"
                class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 text-gray-400 text-xs">{{ u.id }}</td>
              <td class="px-4 py-3 font-semibold text-gray-800">{{ u.usuario }}</td>
              <td class="px-4 py-3 text-gray-600">{{ u.nombre }} {{ u.apellido }}</td>
              <td class="px-4 py-3 text-gray-500">{{ u.email }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="u.sucursales?.length"
                      class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                  {{ u.sucursales.length }} sucursal{{ u.sucursales.length !== 1 ? 'es' : '' }}
                </span>
                <span v-else class="text-gray-300 text-xs">—</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="u.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'">
                  {{ u.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="abrirEditar(u)"
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

        <div class="px-4 py-2 border-t border-gray-100 text-xs text-gray-400">
          {{ usuariosFiltrados.length }} usuario{{ usuariosFiltrados.length !== 1 ? 's' : '' }}
          {{ busqueda ? 'encontrados' : 'en total' }}
        </div>
      </div>
    </div>

    <!-- ───────── MODAL ───────── -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modalAbierto"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
             @click.self="modalAbierto = false">
          <div class="bg-white rounded-2xl shadow-2xl w-full"
               :class="modoEdicion ? 'max-w-xl' : 'max-w-md'">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar usuario' : 'Nuevo usuario' }}
              </h3>
              <button @click="modalAbierto = false"
                      class="w-8 h-8 flex items-center justify-center rounded-lg
                             text-gray-400 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Tabs (solo en edición) -->
            <div v-if="modoEdicion" class="flex border-b border-gray-100 px-6">
              <button
                @click="tabActiva = 'datos'"
                class="py-3 mr-6 text-sm font-medium border-b-2 transition-colors"
                :class="tabActiva === 'datos'
                  ? 'border-orange-500 text-orange-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700'"
              >
                Datos
              </button>
              <button
                @click="tabActiva = 'roles'"
                class="py-3 text-sm font-medium border-b-2 transition-colors"
                :class="tabActiva === 'roles'
                  ? 'border-orange-500 text-orange-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700'"
              >
                Roles / Sucursales
                <span v-if="asignaciones.length"
                      class="ml-1.5 px-1.5 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                  {{ asignaciones.length }}
                </span>
              </button>
            </div>

            <!-- ── TAB DATOS ── -->
            <form v-if="tabActiva === 'datos'" @submit.prevent="guardar" class="px-6 py-5 space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.nombre" type="text" required
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Apellido <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.apellido" type="text" required
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Usuario <span class="text-red-500">*</span>
                </label>
                <input v-model="form.usuario" type="text" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="form.email" type="email"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Contraseña {{ modoEdicion ? '(dejar vacío para no cambiar)' : '*' }}
                </label>
                <input v-model="form.password" type="password" :required="!modoEdicion"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>
              <div class="flex items-center gap-2">
                <input v-model="form.activo" type="checkbox" id="chkActivo"
                       class="w-4 h-4 accent-orange-500"/>
                <label for="chkActivo" class="text-sm text-gray-700">Usuario activo</label>
              </div>

              <!-- Asignación inicial (solo crear) -->
              <template v-if="!modoEdicion">
                <div class="border-t border-gray-100 pt-4">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    Asignación inicial (opcional)
                  </p>
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                      <select v-model="form.sucursal_id"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">— Ninguna —</option>
                        <option v-for="s in sucursales" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                      <select v-model="form.rol_id"
                              :disabled="!form.sucursal_id"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-orange-400
                                     disabled:bg-gray-50 disabled:text-gray-400">
                        <option value="">— Ninguno —</option>
                        <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
                      </select>
                    </div>
                  </div>
                </div>
              </template>

              <p v-if="errorForm" class="text-sm text-red-600 bg-red-50 border border-red-200
                                         rounded-lg px-3 py-2">{{ errorForm }}</p>
              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando...' : (modoEdicion ? 'Actualizar' : 'Crear usuario') }}
                </button>
                <button type="button" @click="modalAbierto = false"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium
                               py-2.5 rounded-lg transition-colors text-sm">
                  Cancelar
                </button>
              </div>
            </form>

            <!-- ── TAB ROLES / SUCURSALES ── -->
            <div v-if="modoEdicion && tabActiva === 'roles'" class="px-6 py-5">

              <!-- Tabla de asignaciones -->
              <div v-if="asignaciones.length > 0" class="mb-5 overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                    <tr>
                      <th class="px-3 py-2 text-left">Sucursal</th>
                      <th class="px-3 py-2 text-left">Rol</th>
                      <th class="px-3 py-2 text-center">Principal</th>
                      <th class="px-3 py-2 text-center">Activo</th>
                      <th class="px-3 py-2 text-center w-16"></th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="a in asignaciones" :key="a.sucursal_id"
                        class="hover:bg-gray-50">
                      <td class="px-3 py-2 text-gray-700">{{ a.nombre }}</td>
                      <td class="px-3 py-2">
                        <select v-model="a.pivot.rol_id"
                                @change="actualizarAsignacion(a)"
                                class="text-xs border border-gray-200 rounded px-2 py-1
                                       focus:outline-none focus:ring-1 focus:ring-orange-300">
                          <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
                        </select>
                      </td>
                      <td class="px-3 py-2 text-center">
                        <button @click="togglePrincipal(a)"
                                :class="a.pivot.es_principal
                                  ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                  : 'bg-gray-100 text-gray-400 hover:bg-gray-200'"
                                class="px-2 py-0.5 rounded-full text-xs font-medium transition-colors">
                          {{ a.pivot.es_principal ? 'Sí' : 'No' }}
                        </button>
                      </td>
                      <td class="px-3 py-2 text-center">
                        <button @click="toggleActivo(a)"
                                :class="a.pivot.activo
                                  ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                  : 'bg-red-100 text-red-600 hover:bg-red-200'"
                                class="px-2 py-0.5 rounded-full text-xs font-medium transition-colors">
                          {{ a.pivot.activo ? 'Activo' : 'Inactivo' }}
                        </button>
                      </td>
                      <td class="px-3 py-2 text-center">
                        <button @click="eliminarAsignacion(a)"
                                class="w-6 h-6 inline-flex items-center justify-center rounded
                                       text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                title="Quitar asignación">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="mb-5 py-6 text-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm">Sin asignaciones — agrega una sucursal y rol abajo</p>
              </div>

              <!-- Agregar nueva asignación -->
              <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                  Agregar asignación
                </p>
                <div class="flex gap-2 flex-wrap items-end">
                  <div class="flex-1 min-w-32">
                    <label class="block text-xs text-gray-600 mb-1">Sucursal <span class="text-red-500">*</span></label>
                    <select v-model="formRol.sucursal_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-orange-400">
                      <option value="">— Selecciona —</option>
                      <option v-for="s in sucursalesDisponibles" :key="s.id" :value="s.id">
                        {{ s.nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="flex-1 min-w-32">
                    <label class="block text-xs text-gray-600 mb-1">Rol <span class="text-red-500">*</span></label>
                    <select v-model="formRol.rol_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-orange-400">
                      <option value="">— Selecciona —</option>
                      <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
                    </select>
                  </div>
                  <div class="flex items-center gap-1.5 pb-2">
                    <input v-model="formRol.es_principal" type="checkbox" id="chkPrincipal"
                           class="w-4 h-4 accent-orange-500"/>
                    <label for="chkPrincipal" class="text-sm text-gray-700 whitespace-nowrap">Principal</label>
                  </div>
                  <button @click="agregarAsignacion"
                          :disabled="!formRol.sucursal_id || !formRol.rol_id || guardandoRol"
                          class="bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white
                                 text-sm font-semibold px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                    {{ guardandoRol ? '...' : 'Agregar' }}
                  </button>
                </div>
                <p v-if="errorRol" class="mt-2 text-xs text-red-600">{{ errorRol }}</p>
              </div>

            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// ── Estado principal ───────────────────────────────────────────────────────────
const usuarios  = ref([]);
const sucursales = ref([]);
const roles      = ref([]);
const busqueda  = ref('');
const cargando  = ref(false);
const errorMsg  = ref('');

// ── Modal ──────────────────────────────────────────────────────────────────────
const modalAbierto  = ref(false);
const modoEdicion   = ref(false);
const guardando     = ref(false);
const errorForm     = ref('');
const tabActiva     = ref('datos');

const form = ref({
  id: null, nombre: '', apellido: '', usuario: '',
  email: '', password: '', activo: true,
  sucursal_id: '', rol_id: '',   // para creación rápida
});

// ── Tab Roles ──────────────────────────────────────────────────────────────────
const asignaciones  = ref([]);   // copia editable de u.sucursales
const formRol       = ref({ sucursal_id: '', rol_id: '', es_principal: false });
const guardandoRol  = ref(false);
const errorRol      = ref('');

// Sucursales que aún no están asignadas a este usuario
const sucursalesDisponibles = computed(() =>
  sucursales.value.filter(s => !asignaciones.value.find(a => a.id === s.id))
);

// ── Filtrado ───────────────────────────────────────────────────────────────────
const usuariosFiltrados = computed(() => {
  const q = busqueda.value.toLowerCase();
  if (!q) return usuarios.value;
  return usuarios.value.filter(u =>
    u.usuario?.toLowerCase().includes(q) ||
    u.nombre?.toLowerCase().includes(q)  ||
    u.apellido?.toLowerCase().includes(q)||
    u.email?.toLowerCase().includes(q)
  );
});

// ── Carga de datos ─────────────────────────────────────────────────────────────
async function cargar() {
  cargando.value = true;
  errorMsg.value = '';
  try {
    const { data } = await axios.get('/api/usuarios');
    usuarios.value = Array.isArray(data) ? data : (data.data ?? []);
  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Error al cargar usuarios';
    console.error('AdminUsuario cargar:', e);
  } finally {
    cargando.value = false;
  }
}

async function cargarCatalogos() {
  try {
    const [rSuc, rRol] = await Promise.all([
      axios.get('/api/sucursales'),
      axios.get('/api/roles'),
    ]);
    sucursales.value = Array.isArray(rSuc.data) ? rSuc.data : (rSuc.data.data ?? []);
    roles.value      = Array.isArray(rRol.data) ? rRol.data : (rRol.data.data ?? []);
  } catch (e) {
    console.error('AdminUsuario cargarCatalogos:', e);
  }
}

// ── Helpers ────────────────────────────────────────────────────────────────────
function rolNombre(rolId) {
  return roles.value.find(r => r.id === rolId)?.nombre ?? '—';
}

// ── Abrir modal ────────────────────────────────────────────────────────────────
function abrirCrear() {
  form.value = {
    id: null, nombre: '', apellido: '', usuario: '',
    email: '', password: '', activo: true,
    sucursal_id: '', rol_id: '',
  };
  asignaciones.value = [];
  modoEdicion.value  = false;
  tabActiva.value    = 'datos';
  modalAbierto.value = true;
  errorForm.value    = '';
}

function abrirEditar(u) {
  form.value = {
    id: u.id, nombre: u.nombre, apellido: u.apellido,
    usuario: u.usuario, email: u.email ?? '', password: '', activo: !!u.activo,
  };
  // Copia profunda de las asignaciones para edición local
  asignaciones.value = (u.sucursales ?? []).map(s => ({
    id:          s.id,
    sucursal_id: s.id,
    nombre:      s.nombre,
    pivot: {
      rol_id:       s.pivot?.rol_id       ?? null,
      es_principal: !!s.pivot?.es_principal,
      activo:       s.pivot?.activo !== undefined ? !!s.pivot.activo : true,
    },
  }));
  formRol.value      = { sucursal_id: '', rol_id: '', es_principal: false };
  modoEdicion.value  = true;
  tabActiva.value    = 'datos';
  modalAbierto.value = true;
  errorForm.value    = '';
  errorRol.value     = '';
}

// ── Guardar datos usuario ──────────────────────────────────────────────────────
async function guardar() {
  guardando.value = true;
  errorForm.value = '';
  try {
    const payload = { ...form.value };
    if (!payload.password) delete payload.password;
    // sucursal_id/rol_id solo en creación y solo si ambos están presentes
    if (modoEdicion.value || !payload.sucursal_id || !payload.rol_id) {
      delete payload.sucursal_id;
      delete payload.rol_id;
    }
    if (modoEdicion.value) {
      await axios.put(`/api/usuarios/${form.value.id}`, payload);
    } else {
      await axios.post('/api/usuarios', payload);
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

// ── Gestión de asignaciones ────────────────────────────────────────────────────
async function agregarAsignacion() {
  if (!formRol.value.sucursal_id || !formRol.value.rol_id) return;
  guardandoRol.value = true;
  errorRol.value     = '';
  try {
    await axios.post(`/api/usuarios/${form.value.id}/sucursales`, {
      sucursal_id:  formRol.value.sucursal_id,
      rol_id:       formRol.value.rol_id,
      es_principal: formRol.value.es_principal,
      activo:       true,
    });
    // Recargar usuario para tener los datos frescos
    const { data } = await axios.get(`/api/usuarios/${form.value.id}`);
    const usuario = data;
    asignaciones.value = (usuario.sucursales ?? []).map(s => ({
      id:          s.id,
      sucursal_id: s.id,
      nombre:      s.nombre,
      pivot: {
        rol_id:       s.pivot?.rol_id       ?? null,
        es_principal: !!s.pivot?.es_principal,
        activo:       s.pivot?.activo !== undefined ? !!s.pivot.activo : true,
      },
    }));
    // Actualizar también la lista principal
    await cargar();
    formRol.value = { sucursal_id: '', rol_id: '', es_principal: false };
  } catch (e) {
    errorRol.value = e.response?.data?.message ?? 'Error al agregar asignación';
  } finally {
    guardandoRol.value = false;
  }
}

async function actualizarAsignacion(a) {
  try {
    await axios.post(`/api/usuarios/${form.value.id}/sucursales`, {
      sucursal_id:  a.sucursal_id,
      rol_id:       a.pivot.rol_id,
      es_principal: a.pivot.es_principal,
      activo:       a.pivot.activo,
    });
  } catch (e) {
    errorRol.value = e.response?.data?.message ?? 'Error al actualizar';
  }
}

async function togglePrincipal(a) {
  a.pivot.es_principal = !a.pivot.es_principal;
  if (a.pivot.es_principal) {
    // Quitar principal de las demás localmente
    asignaciones.value.forEach(x => {
      if (x.sucursal_id !== a.sucursal_id) x.pivot.es_principal = false;
    });
  }
  await actualizarAsignacion(a);
}

async function toggleActivo(a) {
  a.pivot.activo = !a.pivot.activo;
  await actualizarAsignacion(a);
}

async function eliminarAsignacion(a) {
  if (!confirm(`¿Quitar al usuario de "${a.nombre}"?`)) return;
  try {
    await axios.delete(`/api/usuarios/${form.value.id}/sucursales/${a.sucursal_id}`);
    asignaciones.value = asignaciones.value.filter(x => x.sucursal_id !== a.sucursal_id);
    await cargar();
  } catch (e) {
    errorRol.value = e.response?.data?.message ?? 'Error al eliminar asignación';
  }
}

onMounted(async () => {
  await Promise.all([cargar(), cargarCatalogos()]);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
