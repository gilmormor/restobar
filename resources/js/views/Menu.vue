<template>
  <div class="flex flex-col h-full gap-4">

    <!-- ── Header ─────────────────────────────────────────── -->
    <div class="flex items-center justify-between gap-3 flex-wrap">

      <!-- Tabs de categorías (scroll horizontal en móvil) -->
      <div class="flex gap-2 overflow-x-auto pb-1 flex-1 min-w-0 scrollbar-hide">
        <button
          @click="categoriaActiva = null"
          class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="categoriaActiva === null
            ? 'bg-gray-800 text-white'
            : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm'"
        >Todos</button>

        <button
          v-for="cat in categorias"
          :key="cat.id"
          @click="categoriaActiva = cat.id"
          class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="categoriaActiva === cat.id
            ? 'bg-orange-500 text-white'
            : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm'"
        >
          <span>{{ cat.icono || '🍽️' }}</span>
          <span>{{ cat.nombre }}</span>
          <span
            class="text-xs px-1.5 py-0.5 rounded-full font-normal"
            :class="categoriaActiva === cat.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'"
          >{{ cat.platos_count }}</span>
        </button>

        <button
          @click="abrirModalCategoria()"
          class="flex-shrink-0 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-orange-500 border border-dashed border-gray-300 hover:border-orange-300 transition-colors"
        >+ Categoría</button>
      </div>

      <!-- Botón nuevo plato -->
      <AppButton
        @click="abrirModalPlato()"
        class="flex-shrink-0 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm"
      >+ Nuevo Plato</AppButton>
    </div>

    <!-- ── Grid de platos ──────────────────────────────────── -->
    <div class="flex-1 overflow-y-auto">

      <div v-if="platosFiltrados.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
        <span class="text-5xl mb-3">🍽️</span>
        <p class="text-sm">No hay platos en esta categoría.</p>
        <button @click="abrirModalPlato()" class="mt-3 text-sm text-orange-500 hover:underline">Agregar el primero</button>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div
          v-for="plato in platosFiltrados"
          :key="plato.id"
          class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden transition-shadow hover:shadow-md"
          :class="{ 'opacity-60': !plato.disponible }"
        >
          <!-- Cabecera con color de categoría -->
          <div
            class="h-24 flex items-center justify-center text-5xl"
            :class="colorCat(plato.categoria_id).bg"
          >
            {{ plato.categoria?.icono || '🍽️' }}
          </div>

          <!-- Cuerpo -->
          <div class="flex-1 p-3 flex flex-col gap-1">
            <h3 class="font-semibold text-gray-800 text-sm leading-tight line-clamp-2">{{ plato.nombre }}</h3>
            <span
              class="text-xs px-2 py-0.5 rounded-full self-start font-medium"
              :class="colorCat(plato.categoria_id).badge"
            >{{ plato.categoria?.nombre }}</span>
            <p class="text-base font-bold text-green-600 mt-1">${{ formatPrecio(plato.precio) }}</p>

            <!-- Toggle disponibilidad -->
            <div class="flex items-center gap-2 mt-1">
              <button
                @click="toggleDisponibilidad(plato)"
                :disabled="!!loadingDisponible[plato.id]"
                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none disabled:opacity-50"
                :class="plato.disponible ? 'bg-green-500' : 'bg-gray-300'"
              >
                <span
                  class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform duration-200"
                  :class="plato.disponible ? 'translate-x-4' : 'translate-x-0.5'"
                />
              </button>
              <span class="text-xs" :class="plato.disponible ? 'text-green-600' : 'text-gray-400'">
                {{ plato.disponible ? 'Disponible' : 'No disponible' }}
              </span>
              <svg v-if="loadingDisponible[plato.id]" class="animate-spin w-3 h-3 text-gray-400" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/>
                <path d="M12 2 a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
              </svg>
            </div>

            <!-- Motivo si no disponible -->
            <p v-if="!plato.disponible && plato.motivo_no_disponible" class="text-xs text-gray-400 italic leading-tight">
              {{ plato.motivo_no_disponible }}
            </p>
          </div>

          <!-- Acciones -->
          <div class="border-t border-gray-100 flex">
            <AppButton
              @click="abrirModalPlato(plato)"
              class="flex-1 py-2 text-xs text-blue-600 hover:bg-blue-50 transition-colors justify-center"
            >✏️ Editar</AppButton>
            <div class="w-px bg-gray-100"></div>
            <AppButton
              :loading="!!loadingEliminar[plato.id]"
              @click="eliminarPlato(plato)"
              class="flex-1 py-2 text-xs text-red-500 hover:bg-red-50 transition-colors justify-center"
            >🗑️</AppButton>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Modal Crear / Editar Plato ──────────────────────── -->
    <Transition name="modal">
      <div v-if="mostrarModalPlato" class="fixed inset-0 bg-black/50 flex items-end md:items-center justify-center z-50 p-0 md:p-4">
        <div class="bg-white rounded-t-2xl md:rounded-xl shadow-xl w-full md:max-w-lg max-h-[90vh] flex flex-col">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-800">{{ modoEdicionPlato ? 'Editar Plato' : 'Nuevo Plato' }}</h3>
            <button @click="cerrarModalPlato" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 text-xl">&times;</button>
          </div>

          <div class="overflow-y-auto flex-1 px-6 py-4 space-y-4">

            <div>
              <label class="text-sm font-medium text-gray-700 mb-1 block">Nombre del plato <span class="text-red-500">*</span></label>
              <input v-model="formPlato.nombre" type="text" placeholder="Ej: Lomo Saltado"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Categoría <span class="text-red-500">*</span></label>
                <select v-model="formPlato.categoria_id"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                  <option value="" disabled>Seleccionar...</option>
                  <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.icono }} {{ cat.nombre }}</option>
                </select>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700 mb-1 block">Precio <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input v-model="formPlato.precio" type="number" step="0.01" min="0" placeholder="0"
                    class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" />
                </div>
              </div>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-700 mb-1 block">Descripción</label>
              <textarea v-model="formPlato.descripcion" rows="2" placeholder="Ingredientes principales, preparación..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 resize-none" />
            </div>

            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-700">Disponible en carta</p>
                  <p class="text-xs text-gray-400">Los clientes podrán pedirlo</p>
                </div>
                <button
                  @click="formPlato.disponible = !formPlato.disponible"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                  :class="formPlato.disponible ? 'bg-green-500' : 'bg-gray-300'"
                >
                  <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                    :class="formPlato.disponible ? 'translate-x-6' : 'translate-x-1'" />
                </button>
              </div>
              <div v-if="!formPlato.disponible">
                <label class="text-xs font-medium text-gray-600 mb-1 block">Motivo (opcional)</label>
                <input v-model="formPlato.motivo_no_disponible" type="text" placeholder="Ej: Temporalmente agotado"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-white" />
              </div>
            </div>

          </div>

          <div class="flex gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
            <AppButton :loading="loadingGuardarPlato"
              @click="modoEdicionPlato ? actualizarPlato() : crearPlato()"
              class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm"
            >{{ modoEdicionPlato ? 'Guardar cambios' : 'Crear Plato' }}</AppButton>
            <button :disabled="loadingGuardarPlato" @click="cerrarModalPlato"
              class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50">
              Cancelar</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Modal Crear / Editar Categoría ──────────────────── -->
    <Transition name="modal">
      <div v-if="mostrarModalCategoria" class="fixed inset-0 bg-black/50 flex items-end md:items-center justify-center z-50 p-0 md:p-4">
        <div class="bg-white rounded-t-2xl md:rounded-xl shadow-xl w-full md:max-w-md">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">{{ modoEdicionCat ? 'Editar Categoría' : 'Nueva Categoría' }}</h3>
            <button @click="cerrarModalCategoria" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 text-xl">&times;</button>
          </div>

          <div class="px-6 py-4 space-y-4">
            <div class="flex gap-3">
              <div class="w-20">
                <label class="text-sm font-medium text-gray-700 mb-1 block">Ícono</label>
                <input v-model="formCat.icono" type="text" placeholder="🍕" maxlength="4"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-center text-xl focus:outline-none focus:ring-2 focus:ring-orange-300" />
              </div>
              <div class="flex-1">
                <label class="text-sm font-medium text-gray-700 mb-1 block">Nombre <span class="text-red-500">*</span></label>
                <input v-model="formCat.nombre" type="text" placeholder="Ej: Entradas, Carnes, Bebidas..."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" />
              </div>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-700 mb-1 block">Descripción</label>
              <input v-model="formCat.descripcion" type="text" placeholder="Ej: Platos para compartir"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" />
            </div>

            <!-- Preview -->
            <div v-if="formCat.nombre || formCat.icono" class="flex items-center gap-3 bg-orange-50 rounded-xl p-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl bg-white shadow-sm">
                {{ formCat.icono || '🍽️' }}
              </div>
              <div>
                <p class="font-semibold text-gray-800">{{ formCat.nombre || 'Nombre categoría' }}</p>
                <p class="text-xs text-gray-500">{{ formCat.descripcion || 'Sin descripción' }}</p>
              </div>
            </div>
          </div>

          <div class="flex gap-3 px-6 py-4 border-t border-gray-100">
            <AppButton :loading="loadingGuardarCat"
              @click="modoEdicionCat ? actualizarCategoria() : crearCategoria()"
              class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm"
            >{{ modoEdicionCat ? 'Guardar cambios' : 'Crear Categoría' }}</AppButton>
            <button :disabled="loadingGuardarCat" @click="cerrarModalCategoria"
              class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50">
              Cancelar</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Modal motivo no disponible ─────────────────────── -->
    <Transition name="modal">
      <div v-if="mostrarModalMotivo" class="fixed inset-0 bg-black/50 flex items-end md:items-center justify-center z-50 p-0 md:p-4">
        <div class="bg-white rounded-t-2xl md:rounded-xl shadow-xl w-full md:max-w-sm">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">¿Por qué no está disponible?</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ platoToggleando?.nombre }}</p>
          </div>
          <div class="px-6 py-4">
            <input v-model="motivoTemp" type="text"
              placeholder="Ej: Temporalmente agotado, en espera de ingredientes..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
              @keyup.enter="confirmarNoDisponible" />
            <p class="text-xs text-gray-400 mt-1">Opcional — puedes dejarlo en blanco</p>
          </div>
          <div class="flex gap-3 px-6 py-4 border-t border-gray-100">
            <AppButton :loading="!!loadingDisponible[platoToggleando?.id]"
              @click="confirmarNoDisponible"
              class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm"
            >Marcar no disponible</AppButton>
            <button @click="mostrarModalMotivo = false; platoToggleando = null"
              class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50">
              Cancelar</button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import AppButton from '../components/ui/AppButton.vue';

// ── Estado ────────────────────────────────────────────────
const categorias      = ref([]);
const platos          = ref([]);
const categoriaActiva = ref(null);

// Modales plato
const mostrarModalPlato = ref(false);
const modoEdicionPlato  = ref(false);
const platoEditandoId   = ref(null);
const formPlato = ref({
  nombre: '', descripcion: '', precio: '', categoria_id: '',
  disponible: true, motivo_no_disponible: '', updated_at: null,
});

// Modales categoría
const mostrarModalCategoria = ref(false);
const modoEdicionCat        = ref(false);
const catEditandoId         = ref(null);
const formCat = ref({ nombre: '', descripcion: '', icono: '', updated_at: null });

// Modal motivo no disponible
const mostrarModalMotivo = ref(false);
const platoToggleando    = ref(null);
const motivoTemp         = ref('');

// Loading
const loadingGuardarPlato = ref(false);
const loadingGuardarCat   = ref(false);
const loadingDisponible   = ref({});
const loadingEliminar     = ref({});

// ── Paleta de colores por índice de categoría ─────────────
const paleta = [
  { bg: 'bg-orange-100', badge: 'bg-orange-100 text-orange-700' },
  { bg: 'bg-blue-100',   badge: 'bg-blue-100 text-blue-700'     },
  { bg: 'bg-green-100',  badge: 'bg-green-100 text-green-700'   },
  { bg: 'bg-purple-100', badge: 'bg-purple-100 text-purple-700' },
  { bg: 'bg-red-100',    badge: 'bg-red-100 text-red-700'       },
  { bg: 'bg-yellow-100', badge: 'bg-yellow-100 text-yellow-700' },
  { bg: 'bg-pink-100',   badge: 'bg-pink-100 text-pink-700'     },
  { bg: 'bg-indigo-100', badge: 'bg-indigo-100 text-indigo-700' },
  { bg: 'bg-teal-100',   badge: 'bg-teal-100 text-teal-700'     },
];

function colorCat(categoriaId) {
  const idx = categorias.value.findIndex(c => c.id === categoriaId);
  return paleta[(idx >= 0 ? idx : 0) % paleta.length];
}

function formatPrecio(precio) {
  return Number(precio).toLocaleString('es-CO');
}

// ── Computed ──────────────────────────────────────────────
const platosFiltrados = computed(() =>
  categoriaActiva.value === null
    ? platos.value
    : platos.value.filter(p => p.categoria_id === categoriaActiva.value)
);

// ── Helpers ───────────────────────────────────────────────
async function obtenerFrescoPlato(id) {
  const res = await axios.get(`/api/platos/${id}`);
  return res.data;
}
async function obtenerFrescaCat(id) {
  const res = await axios.get(`/api/categorias/${id}`);
  return res.data;
}
async function manejarConflicto(error) {
  if (error.response?.status === 409) {
    await Swal.fire({
      icon: 'warning', title: '⚠️ Registro modificado',
      text: error.response.data.message,
      confirmButtonText: 'Recargar datos', confirmButtonColor: '#f97316',
    });
    await cargarDatos();
    return true;
  }
  return false;
}
function manejarErrorRelacion(error) {
  if (error.response?.status === 422) {
    Swal.fire({ icon: 'error', title: '❌ No se puede eliminar', text: error.response.data.message, confirmButtonColor: '#f97316' });
    return true;
  }
  return false;
}

// ── Toggle disponibilidad ─────────────────────────────────
async function toggleDisponibilidad(plato) {
  if (plato.disponible) {
    platoToggleando.value    = plato;
    motivoTemp.value         = plato.motivo_no_disponible || '';
    mostrarModalMotivo.value = true;
    return;
  }
  await ejecutarToggle(plato, true, null);
}

async function confirmarNoDisponible() {
  if (!platoToggleando.value) return;
  await ejecutarToggle(platoToggleando.value, false, motivoTemp.value);
  mostrarModalMotivo.value = false;
  platoToggleando.value    = null;
  motivoTemp.value         = '';
}

async function ejecutarToggle(plato, disponible, motivo) {
  loadingDisponible.value[plato.id] = true;
  try {
    const fresco = await obtenerFrescoPlato(plato.id);
    const res = await axios.patch(`/api/platos/${fresco.id}/disponibilidad`, {
      disponible, motivo_no_disponible: motivo, updated_at: fresco.updated_at,
    });
    const idx = platos.value.findIndex(p => p.id === plato.id);
    if (idx !== -1) platos.value[idx] = { ...platos.value[idx], ...res.data };
  } catch (error) {
    if (await manejarConflicto(error)) return;
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar la disponibilidad.', confirmButtonColor: '#f97316' });
  } finally {
    delete loadingDisponible.value[plato.id];
  }
}

// ── Modal Plato ───────────────────────────────────────────
function abrirModalPlato(plato = null) {
  modoEdicionPlato.value  = !!plato;
  platoEditandoId.value   = plato?.id ?? null;
  mostrarModalPlato.value = true;
  if (plato) {
    _cargarFormPlato(plato);
  } else {
    formPlato.value = {
      nombre: '', descripcion: '', precio: '',
      categoria_id: categoriaActiva.value || (categorias.value[0]?.id ?? ''),
      disponible: true, motivo_no_disponible: '', updated_at: null,
    };
  }
}

async function _cargarFormPlato(plato) {
  try {
    const fresco = await obtenerFrescoPlato(plato.id);
    formPlato.value = {
      nombre: fresco.nombre, descripcion: fresco.descripcion || '',
      precio: fresco.precio, categoria_id: fresco.categoria_id,
      disponible: fresco.disponible,
      motivo_no_disponible: fresco.motivo_no_disponible || '',
      updated_at: fresco.updated_at,
    };
  } catch {
    cerrarModalPlato();
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el plato.', confirmButtonColor: '#f97316' });
  }
}

function cerrarModalPlato() {
  mostrarModalPlato.value = false;
  modoEdicionPlato.value  = false;
  platoEditandoId.value   = null;
}

// ── CRUD Platos ───────────────────────────────────────────
async function crearPlato() {
  if (!formPlato.value.nombre || !formPlato.value.categoria_id || !formPlato.value.precio) {
    Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Completa nombre, categoría y precio.', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardarPlato.value = true;
  try {
    const res = await axios.post('/api/platos', formPlato.value);
    platos.value.push(res.data);
    const cat = categorias.value.find(c => c.id === res.data.categoria_id);
    if (cat) cat.platos_count++;
    cerrarModalPlato();
    Swal.fire({ icon: 'success', title: 'Plato creado', timer: 1500, showConfirmButton: false });
  } finally {
    loadingGuardarPlato.value = false;
  }
}

async function actualizarPlato() {
  if (!formPlato.value.nombre || !formPlato.value.precio) {
    Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Nombre y precio son obligatorios.', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardarPlato.value = true;
  try {
    const res = await axios.put(`/api/platos/${platoEditandoId.value}`, formPlato.value);
    const idx = platos.value.findIndex(p => p.id === platoEditandoId.value);
    if (idx !== -1) platos.value[idx] = { ...platos.value[idx], ...res.data };
    cerrarModalPlato();
    Swal.fire({ icon: 'success', title: 'Plato actualizado', timer: 1500, showConfirmButton: false });
  } catch (error) {
    if (await manejarConflicto(error)) { cerrarModalPlato(); return; }
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar.', confirmButtonColor: '#f97316' });
  } finally {
    loadingGuardarPlato.value = false;
  }
}

async function eliminarPlato(plato) {
  const result = await Swal.fire({
    title: '¿Eliminar plato?', text: `¿Está seguro que desea eliminar "${plato.nombre}"?`,
    icon: 'warning', showCancelButton: true,
    confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
  });
  if (!result.isConfirmed) return;

  loadingEliminar.value[plato.id] = true;
  try {
    const fresco = await obtenerFrescoPlato(plato.id);
    await axios.delete(`/api/platos/${fresco.id}`, { data: { updated_at: fresco.updated_at } });
    const catId = plato.categoria_id;
    platos.value = platos.value.filter(p => p.id !== plato.id);
    const cat = categorias.value.find(c => c.id === catId);
    if (cat && cat.platos_count > 0) cat.platos_count--;
    Swal.fire({ icon: 'success', title: 'Plato eliminado', timer: 1500, showConfirmButton: false });
  } catch (error) {
    if (await manejarConflicto(error)) return;
    if (manejarErrorRelacion(error)) return;
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar.', confirmButtonColor: '#f97316' });
  } finally {
    delete loadingEliminar.value[plato.id];
  }
}

// ── Modal Categoría ───────────────────────────────────────
function abrirModalCategoria(cat = null) {
  modoEdicionCat.value        = !!cat;
  catEditandoId.value         = cat?.id ?? null;
  mostrarModalCategoria.value = true;
  if (cat) {
    _cargarFormCat(cat);
  } else {
    formCat.value = { nombre: '', descripcion: '', icono: '', updated_at: null };
  }
}

async function _cargarFormCat(cat) {
  try {
    const fresca = await obtenerFrescaCat(cat.id);
    formCat.value = {
      nombre: fresca.nombre, descripcion: fresca.descripcion || '',
      icono: fresca.icono || '', updated_at: fresca.updated_at,
    };
  } catch {
    cerrarModalCategoria();
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la categoría.', confirmButtonColor: '#f97316' });
  }
}

function cerrarModalCategoria() {
  mostrarModalCategoria.value = false;
  modoEdicionCat.value        = false;
  catEditandoId.value         = null;
}

// ── CRUD Categorías ───────────────────────────────────────
async function crearCategoria() {
  if (!formCat.value.nombre) {
    Swal.fire({ icon: 'warning', title: 'Nombre requerido', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardarCat.value = true;
  try {
    const res = await axios.post('/api/categorias', formCat.value);
    categorias.value.push({ ...res.data, platos_count: 0 });
    cerrarModalCategoria();
    Swal.fire({ icon: 'success', title: 'Categoría creada', timer: 1500, showConfirmButton: false });
  } finally {
    loadingGuardarCat.value = false;
  }
}

async function actualizarCategoria() {
  if (!formCat.value.nombre) {
    Swal.fire({ icon: 'warning', title: 'Nombre requerido', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardarCat.value = true;
  try {
    const res = await axios.put(`/api/categorias/${catEditandoId.value}`, formCat.value);
    const idx = categorias.value.findIndex(c => c.id === catEditandoId.value);
    if (idx !== -1) categorias.value[idx] = { ...categorias.value[idx], ...res.data };
    cerrarModalCategoria();
    Swal.fire({ icon: 'success', title: 'Categoría actualizada', timer: 1500, showConfirmButton: false });
  } catch (error) {
    if (await manejarConflicto(error)) { cerrarModalCategoria(); return; }
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar.', confirmButtonColor: '#f97316' });
  } finally {
    loadingGuardarCat.value = false;
  }
}

// ── Cargar datos ──────────────────────────────────────────
async function cargarDatos() {
  const [resCat, resPlatos] = await Promise.all([
    axios.get('/api/categorias'),
    axios.get('/api/platos'),
  ]);
  categorias.value = resCat.data;
  platos.value     = resPlatos.data;
}

onMounted(cargarDatos);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.modal-enter-active { transition: opacity 0.25s ease-out; }
.modal-leave-active { transition: opacity 0.2s ease-in; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
