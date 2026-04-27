<template>
  <div class="flex flex-col h-full">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="ambiente in ambientes"
          :key="ambiente.id"
          @click="ambienteActivo = ambiente.id"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="ambienteActivo === ambiente.id
            ? 'bg-orange-500 text-white'
            : 'bg-white text-gray-600 hover:bg-gray-50 shadow-sm'"
        >
          {{ ambiente.nombre }}
        </button>
        <button
          @click="mostrarModalAmbiente = true"
          class="px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-orange-500 border border-dashed border-gray-300 hover:border-orange-300"
        >
          + Ambiente
        </button>
      </div>
      <button
        @click="abrirModalNuevaMesa"
        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium"
      >
        + Nueva Mesa
      </button>
    </div>

    <!-- Leyenda -->
    <div class="flex gap-4 mb-3">
      <div v-for="estado in estados" :key="estado.valor" class="flex items-center gap-1.5 text-xs text-gray-600">
        <div class="w-2.5 h-2.5 rounded-full" :class="estado.color"></div>
        {{ estado.label }}
      </div>
    </div>

    <!-- VISTA PC: Plano drag & drop -->
    <div class="hidden md:flex flex-col flex-1 min-h-0">
      <div
        class="relative bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-200 flex-1"
        style="min-height: 400px;"
        @click.self="mesaSeleccionada = null"
      >
        <div
          v-for="mesa in mesasFiltradas"
          :key="mesa.id"
          class="absolute cursor-move select-none"
          :style="{ left: mesa.pos_x + 'px', top: mesa.pos_y + 'px' }"
          @mousedown="iniciarArrastre($event, mesa)"
          @click.stop="seleccionarMesa(mesa)"
        >
          <div
            class="w-20 h-20 rounded-xl flex flex-col items-center justify-center shadow-md border-2 transition-all"
            :class="clasesMesa(mesa)"
          >
            <span class="text-2xl">🪑</span>
            <span class="text-xs font-bold mt-1">Mesa {{ mesa.numero }}</span>
            <span class="text-xs">{{ mesa.capacidad }}p</span>
          </div>
        </div>

        <div v-if="mesasFiltradas.length === 0" class="absolute inset-0 flex items-center justify-center">
          <p class="text-gray-400 text-sm">No hay mesas. Crea una con el botón "Nueva Mesa".</p>
        </div>
      </div>

      <!-- Panel mesa seleccionada PC -->
      <div v-if="mesaSeleccionada" class="mt-3 bg-white rounded-xl shadow-sm p-3 flex items-center justify-between flex-wrap gap-2">
        <div>
          <h3 class="font-semibold text-gray-800">Mesa {{ mesaSeleccionada.numero }}</h3>
          <p class="text-xs text-gray-500">
            Capacidad: {{ mesaSeleccionada.capacidad }} personas —
            Estado: <span class="font-medium capitalize">{{ mesaSeleccionada.estado }}</span>
          </p>
        </div>
        <div class="flex gap-2 flex-wrap">
          <AppButton
            v-for="estado in estados"
            :key="estado.valor"
            :loading="loadingEstado[mesaSeleccionada.id] === estado.valor"
            @click="cambiarEstado(mesaSeleccionada, estado.valor)"
            class="px-3 py-1.5 rounded-lg text-xs border transition-colors"
            :class="mesaSeleccionada.estado === estado.valor
              ? 'border-orange-500 bg-orange-50 text-orange-700'
              : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
          >{{ estado.label }}</AppButton>
          <AppButton
            @click="abrirModalEditar(mesaSeleccionada)"
            class="px-3 py-1.5 rounded-lg text-xs border border-blue-200 text-blue-600 hover:bg-blue-50"
          >✏️ Editar</AppButton>
          <AppButton
            :loading="!!loadingEliminar[mesaSeleccionada.id]"
            @click="eliminarMesa(mesaSeleccionada)"
            class="px-3 py-1.5 rounded-lg text-xs border border-red-200 text-red-600 hover:bg-red-50"
          >🗑️ Eliminar</AppButton>
        </div>
      </div>
    </div>

    <!-- VISTA MÓVIL: Grilla de tarjetas -->
    <div class="md:hidden flex-1 overflow-y-auto">
      <div v-if="mesasFiltradas.length === 0" class="text-center text-gray-400 text-sm py-10">
        No hay mesas en este ambiente.
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="mesa in mesasFiltradas"
          :key="mesa.id"
          @click="abrirAccionesMobile(mesa)"
          class="bg-white rounded-xl shadow-sm border-2 p-4 flex flex-col items-center cursor-pointer active:scale-95 transition-transform"
          :class="clasesMesaMobile(mesa)"
        >
          <span class="text-4xl mb-2">🪑</span>
          <span class="font-bold text-gray-800">Mesa {{ mesa.numero }}</span>
          <span class="text-xs text-gray-500">{{ mesa.capacidad }} personas</span>
          <span
            class="mt-2 px-2 py-0.5 rounded-full text-xs font-medium capitalize"
            :class="badgeEstado(mesa.estado)"
          >{{ mesa.estado }}</span>
        </div>
      </div>
    </div>

    <!-- Modal Nueva / Editar Mesa -->
    <div v-if="mostrarModalMesa" class="fixed inset-0 bg-black/50 flex items-end md:items-center justify-center z-50">
      <div class="bg-white rounded-t-2xl md:rounded-xl shadow-xl p-6 w-full md:w-96">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          {{ modoEdicion ? 'Editar Mesa' : 'Nueva Mesa' }}
        </h3>
        <div class="space-y-3">
          <div>
            <label class="text-sm text-gray-600 mb-1 block">Número de mesa</label>
            <input v-model="formMesa.numero" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" placeholder="Ej: 1" />
          </div>
          <div>
            <label class="text-sm text-gray-600 mb-1 block">Capacidad (personas)</label>
            <input v-model="formMesa.capacidad" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" placeholder="Ej: 4" />
          </div>
          <div>
            <label class="text-sm text-gray-600 mb-1 block">Ambiente</label>
            <select v-model="formMesa.ambiente_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
              <option v-for="a in ambientes" :key="a.id" :value="a.id">{{ a.nombre }}</option>
            </select>
          </div>
        </div>
        <div class="flex gap-2 mt-5">
          <AppButton
            :loading="loadingGuardar"
            @click="modoEdicion ? actualizarMesa() : crearMesa()"
            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm"
          >{{ modoEdicion ? 'Guardar cambios' : 'Crear Mesa' }}</AppButton>
          <button
            :disabled="loadingGuardar"
            @click="cerrarModalMesa"
            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Modal Nuevo Ambiente -->
    <div v-if="mostrarModalAmbiente" class="fixed inset-0 bg-black/50 flex items-end md:items-center justify-center z-50">
      <div class="bg-white rounded-t-2xl md:rounded-xl shadow-xl p-6 w-full md:w-96">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Nuevo Ambiente</h3>
        <div class="space-y-3">
          <div>
            <label class="text-sm text-gray-600 mb-1 block">Nombre</label>
            <input v-model="nuevoAmbiente.nombre" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" placeholder="Ej: Salón Principal" />
          </div>
          <div>
            <label class="text-sm text-gray-600 mb-1 block">Descripción (opcional)</label>
            <input v-model="nuevoAmbiente.descripcion" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" placeholder="Ej: Planta baja" />
          </div>
        </div>
        <div class="flex gap-2 mt-5">
          <AppButton
            :loading="loadingAmbiente"
            @click="crearAmbiente"
            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm"
          >Crear Ambiente</AppButton>
          <button
            :disabled="loadingAmbiente"
            @click="mostrarModalAmbiente = false"
            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >Cancelar</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import AppButton from '../components/ui/AppButton.vue';

const ambientes        = ref([]);
const mesas            = ref([]);
const ambienteActivo   = ref(null);
const mesaSeleccionada = ref(null);
const mostrarModalMesa     = ref(false);
const mostrarModalAmbiente = ref(false);
const modoEdicion      = ref(false);
const mesaEditandoId   = ref(null);

const formMesa      = ref({ numero: '', capacidad: 4, ambiente_id: null, updated_at: null });
const nuevoAmbiente = ref({ nombre: '', descripcion: '' });

// Estados de carga por operación (para spinners individuales en cada botón)
const loadingEstado   = ref({});   // { [mesaId]: true/false }
const loadingEliminar = ref({});   // { [mesaId]: true/false }
const loadingGuardar  = ref(false);
const loadingAmbiente = ref(false);

const estados = [
  { valor: 'libre',     label: 'Libre',     color: 'bg-green-400' },
  { valor: 'ocupada',   label: 'Ocupada',   color: 'bg-red-400' },
  { valor: 'reservada', label: 'Reservada', color: 'bg-yellow-400' },
];

const mesasFiltradas = computed(() =>
  mesas.value.filter(m => m.ambiente_id === ambienteActivo.value)
);

function clasesMesa(mesa) {
  const clases = {
    libre:     'bg-green-50 border-green-400 text-green-700',
    ocupada:   'bg-red-50 border-red-400 text-red-700',
    reservada: 'bg-yellow-50 border-yellow-400 text-yellow-700',
    cerrada:   'bg-gray-100 border-gray-300 text-gray-500',
  };
  const sel = mesaSeleccionada.value?.id === mesa.id ? ' ring-2 ring-orange-500 ring-offset-2' : '';
  return (clases[mesa.estado] || clases.libre) + sel;
}

function clasesMesaMobile(mesa) {
  return {
    libre:     'border-green-300',
    ocupada:   'border-red-300',
    reservada: 'border-yellow-300',
    cerrada:   'border-gray-200',
  }[mesa.estado] || 'border-green-300';
}

function badgeEstado(estado) {
  return {
    libre:     'bg-green-100 text-green-700',
    ocupada:   'bg-red-100 text-red-700',
    reservada: 'bg-yellow-100 text-yellow-700',
    cerrada:   'bg-gray-100 text-gray-600',
  }[estado] || 'bg-green-100 text-green-700';
}

function seleccionarMesa(mesa) {
  mesaSeleccionada.value = mesaSeleccionada.value?.id === mesa.id ? null : mesa;
}

// ── Acciones móvil ───────────────────────────────────────
async function abrirAccionesMobile(mesa) {
  const { value: accion } = await Swal.fire({
    title: `Mesa ${mesa.numero}`,
    html: `<p class="text-sm text-gray-500">Capacidad: ${mesa.capacidad} personas</p>
           <p class="text-sm font-medium mt-1 capitalize">Estado: ${mesa.estado}</p>`,
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: '✏️ Editar',
    denyButtonText: '🔄 Cambiar estado',
    cancelButtonText: '❌ Cerrar',
    confirmButtonColor: '#3b82f6',
    denyButtonColor: '#f97316',
  });

  if (accion === true) {
    abrirModalEditar(mesa);
  } else if (accion === false) {
    cambiarEstadoMobile(mesa);
  }
}

async function cambiarEstadoMobile(mesa) {
  const { value: nuevoEstado } = await Swal.fire({
    title: `Cambiar estado — Mesa ${mesa.numero}`,
    input: 'select',
    inputOptions: { libre: '🟢 Libre', ocupada: '🔴 Ocupada', reservada: '🟡 Reservada' },
    inputValue: mesa.estado,
    confirmButtonText: 'Cambiar',
    confirmButtonColor: '#f97316',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
  });
  if (nuevoEstado) {
    await cambiarEstado(mesa, nuevoEstado);
  }
}

// ── Drag & Drop PC ───────────────────────────────────────
let arrastrando = null, offsetX = 0, offsetY = 0;

function iniciarArrastre(event, mesa) {
  arrastrando = mesa;
  offsetX = event.clientX - mesa.pos_x;
  offsetY = event.clientY - mesa.pos_y;
  document.addEventListener('mousemove', moverMesa);
  document.addEventListener('mouseup', soltarMesa);
}

function moverMesa(event) {
  if (!arrastrando) return;
  arrastrando.pos_x = Math.max(0, event.clientX - offsetX);
  arrastrando.pos_y = Math.max(0, event.clientY - offsetY);
}

async function soltarMesa() {
  if (!arrastrando) return;
  await axios.patch(`/api/mesas/${arrastrando.id}/posicion`, { pos_x: arrastrando.pos_x, pos_y: arrastrando.pos_y });
  arrastrando = null;
  document.removeEventListener('mousemove', moverMesa);
  document.removeEventListener('mouseup', soltarMesa);
}

// ── Helper: obtener datos frescos del servidor ───────────
// Se llama al hacer clic en cualquier botón que modifique un registro.
// Retorna el registro con el updated_at actual de la BD en ese instante.
async function obtenerFresco(id) {
  const res = await axios.get(`/api/mesas/${id}`);
  return res.data;
}

// ── Helper: manejar error 409 (conflicto de concurrencia) ─
async function manejarConflicto(error) {
  if (error.response?.status === 409) {
    await Swal.fire({
      icon: 'warning',
      title: '⚠️ Registro modificado',
      text: error.response.data.message,
      confirmButtonText: 'Recargar datos',
      confirmButtonColor: '#f97316',
    });
    await cargarDatos();
    return true;   // fue un conflicto, el llamador debe detenerse
  }
  return false;
}

// ── Estado ───────────────────────────────────────────────
async function cambiarEstado(mesa, estado) {
  loadingEstado.value[mesa.id] = estado; // guarda qué estado está procesando
  try {
    const fresca = await obtenerFresco(mesa.id);
    const res = await axios.patch(`/api/mesas/${fresca.id}/estado`, {
      estado,
      updated_at: fresca.updated_at,
    });
    const idx = mesas.value.findIndex(m => m.id === mesa.id);
    if (idx !== -1) mesas.value[idx] = { ...mesas.value[idx], ...res.data };
    if (mesaSeleccionada.value?.id === mesa.id) mesaSeleccionada.value = { ...mesaSeleccionada.value, ...res.data };
  } catch (error) {
    if (await manejarConflicto(error)) return;
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cambiar el estado.', confirmButtonColor: '#f97316' });
  } finally {
    delete loadingEstado.value[mesa.id];
  }
}

// ── Modales ──────────────────────────────────────────────
function abrirModalNuevaMesa() {
  modoEdicion.value    = false;
  mesaEditandoId.value = null;
  formMesa.value       = { numero: '', capacidad: 4, ambiente_id: ambienteActivo.value };
  mostrarModalMesa.value = true;
}

async function abrirModalEditar(mesa) {
  try {
    // Consultamos el registro fresco del servidor en el momento exacto
    // que el usuario hace clic en Editar. Así el updated_at es el real
    // y si alguien lo modificó mientras la pantalla estaba abierta, lo detectamos.
    const res = await axios.get(`/api/mesas/${mesa.id}`);
    const fresca = res.data;

    modoEdicion.value    = true;
    mesaEditandoId.value = fresca.id;
    formMesa.value = {
      numero:      fresca.numero,
      capacidad:   fresca.capacidad,
      ambiente_id: fresca.ambiente_id,
      updated_at:  fresca.updated_at,  // ← tomado del servidor en este instante
    };
    mostrarModalMesa.value = true;
  } catch {
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la mesa. Intenta de nuevo.', confirmButtonColor: '#f97316' });
  }
}

function cerrarModalMesa() {
  mostrarModalMesa.value = false;
  modoEdicion.value      = false;
  mesaEditandoId.value   = null;
}

// ── CRUD Mesas ───────────────────────────────────────────
async function crearMesa() {
  if (!formMesa.value.numero || !formMesa.value.ambiente_id) {
    Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Completa el número de mesa y el ambiente.', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardar.value = true;
  try {
    const res = await axios.post('/api/mesas', { ...formMesa.value, pos_x: 50, pos_y: 50 });
    mesas.value.push(res.data);
    cerrarModalMesa();
    Swal.fire({ icon: 'success', title: 'Mesa creada', timer: 1500, showConfirmButton: false });
  } finally {
    loadingGuardar.value = false;
  }
}

async function actualizarMesa() {
  if (!formMesa.value.numero) {
    Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'El número de mesa es obligatorio.', confirmButtonColor: '#f97316' });
    return;
  }
  loadingGuardar.value = true;
  try {
    const res = await axios.put(`/api/mesas/${mesaEditandoId.value}`, formMesa.value);
    const idx = mesas.value.findIndex(m => m.id === mesaEditandoId.value);
    if (idx !== -1) {
      mesas.value[idx] = { ...mesas.value[idx], ...res.data };
      if (mesaSeleccionada.value?.id === mesaEditandoId.value) mesaSeleccionada.value = mesas.value[idx];
    }
    cerrarModalMesa();
    Swal.fire({ icon: 'success', title: 'Mesa actualizada', timer: 1500, showConfirmButton: false });
  } catch (error) {
    if (await manejarConflicto(error)) { cerrarModalMesa(); return; }
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar. Intenta de nuevo.', confirmButtonColor: '#f97316' });
  } finally {
    loadingGuardar.value = false;
  }
}

async function eliminarMesa(mesa) {
  const result = await Swal.fire({
    title: '¿Eliminar mesa?',
    text: `¿Está seguro que desea eliminar la Mesa ${mesa.numero}? Esta acción no se puede deshacer.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });
  if (result.isConfirmed) {
    loadingEliminar.value[mesa.id] = true;
    try {
      const fresca = await obtenerFresco(mesa.id);
      await axios.delete(`/api/mesas/${fresca.id}`, { data: { updated_at: fresca.updated_at } });
      mesas.value = mesas.value.filter(m => m.id !== fresca.id);
      mesaSeleccionada.value = null;
      Swal.fire({ icon: 'success', title: 'Mesa eliminada', timer: 1500, showConfirmButton: false });
    } catch (error) {
      if (await manejarConflicto(error)) return;
      if (error.response?.status === 422) {
        Swal.fire({ icon: 'error', title: '❌ No se puede eliminar', text: error.response.data.message, confirmButtonColor: '#f97316' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar. Intenta de nuevo.', confirmButtonColor: '#f97316' });
      }
    } finally {
      delete loadingEliminar.value[mesa.id];
    }
  }
}

// ── CRUD Ambientes ───────────────────────────────────────
async function crearAmbiente() {
  if (!nuevoAmbiente.value.nombre) {
    Swal.fire({ icon: 'warning', title: 'Nombre requerido', confirmButtonColor: '#f97316' });
    return;
  }
  loadingAmbiente.value = true;
  try {
    const res = await axios.post('/api/ambientes', nuevoAmbiente.value);
    ambientes.value.push(res.data);
    ambienteActivo.value = res.data.id;
    mostrarModalAmbiente.value = false;
    nuevoAmbiente.value = { nombre: '', descripcion: '' };
    Swal.fire({ icon: 'success', title: 'Ambiente creado', timer: 1500, showConfirmButton: false });
  } finally {
    loadingAmbiente.value = false;
  }
}

// ── Cargar datos ─────────────────────────────────────────
async function cargarDatos() {
  const [resAmbientes, resMesas] = await Promise.all([
    axios.get('/api/ambientes'),
    axios.get('/api/mesas'),
  ]);
  ambientes.value = resAmbientes.data;
  mesas.value     = resMesas.data;
  if (ambientes.value.length > 0) {
    ambienteActivo.value       = ambientes.value[0].id;
    formMesa.value.ambiente_id = ambientes.value[0].id;
  }
}

onMounted(cargarDatos);
</script>
