<template>
  <div>

    <!-- ── Encabezado ─────────────────────────────────────────────────────────── -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Menús</h2>
        <p class="text-sm text-gray-500 mt-0.5">
          Arrastra los ítems para reordenar o anidar dentro de otro
        </p>
      </div>
      <button
        @click="abrirCrear"
        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white
               text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo menú
      </button>
    </div>

    <!-- ── Árbol drag-and-drop ────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

      <!-- Leyenda -->
      <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100
                  bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wide">
        <span class="w-7"/>
        <span class="w-5"/>
        <span class="flex-1">Nombre</span>
        <span class="hidden sm:block">URL / Ruta</span>
        <span class="w-16 text-center">Acciones</span>
      </div>

      <div class="p-3">

        <!-- Loading -->
        <div v-if="cargando" class="space-y-2 py-4">
          <div v-for="n in 6" :key="n"
               class="h-10 bg-gray-100 rounded-lg animate-pulse" />
        </div>

        <!-- Vacío -->
        <div v-else-if="arbol.length === 0"
             class="py-12 text-center text-gray-400">
          <div class="text-4xl mb-2">☰</div>
          <p class="text-sm">No hay menús registrados. Crea el primero.</p>
        </div>

        <!-- Árbol nestable (drag-and-drop) -->
        <VueDraggable
          v-else
          v-model="arbol"
          :group="{ name: 'menus', pull: true, put: true }"
          handle=".drag-handle"
          :animation="200"
          ghost-class="drag-ghost"
          chosen-class="drag-chosen"
          @end="scheduleGuardar"
        >
          <MenuDragItem
            v-for="item in arbol"
            :key="item.id"
            :item="item"
            @reorder="scheduleGuardar"
            @edit="abrirEditar"
            @delete="confirmarEliminar"
          />
        </VueDraggable>

      </div>
    </div>

    <!-- ── Toast de guardado ──────────────────────────────────────────────────── -->
    <Transition name="toast">
      <div
        v-if="toastVisible"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2
               rounded-full px-4 py-2 text-sm font-medium shadow-lg
               text-white transition-all"
        :class="toastError
          ? 'bg-red-600'
          : guardandoOrden ? 'bg-gray-700' : 'bg-green-600'"
      >
        <svg v-if="guardandoOrden" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span v-else>{{ toastError ? '✕' : '✓' }}</span>
        {{ toastError ? toastError : guardandoOrden ? 'Guardando orden…' : 'Orden guardado' }}
      </div>
    </Transition>

    <!-- ── Modal crear / editar ───────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="modalAbierto"
          class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
          @click.self="modalAbierto = false"
        >
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 class="text-base font-bold text-gray-800">
                {{ modoEdicion ? 'Editar menú' : 'Nuevo menú' }}
              </h3>
              <button
                @click="modalAbierto = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                       text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="guardar" class="px-6 py-5 space-y-4">

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Nombre <span class="text-red-500">*</span>
                </label>
                <input v-model="form.nombre" type="text" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  URL / Ruta
                  <span class="text-xs font-normal text-gray-400 ml-1">(vacío = grupo padre "#")</span>
                </label>
                <input v-model="form.ruta" type="text"
                       placeholder="nombre-de-ruta  ó dejar vacío para grupo"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                              focus:outline-none focus:ring-2 focus:ring-orange-400"/>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Ícono
                  <span class="text-xs font-normal text-gray-400 ml-1">(Heroicon o emoji)</span>
                </label>
                <div class="flex items-center gap-2">
                  <input v-model="form.icono" type="text"
                         placeholder="HomeIcon, 🍽️, ShieldCheckIcon…"
                         class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                  <span class="text-2xl w-10 text-center">{{ resolveIconPreview(form.icono) }}</span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Menú padre</label>
                <select v-model="form.menu_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-orange-400">
                  <option :value="null">— Sin padre (nivel raíz) —</option>
                  <option v-for="m in opcionesPadre" :key="m.id" :value="m.id">
                    {{ '· '.repeat(m.nivel) }}{{ m.nombre }}
                  </option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                  <input v-model="form.slug" type="text"
                         placeholder="auto-generado si está vacío"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Orden inicial</label>
                  <input v-model.number="form.orden" type="number" min="1"
                         class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                </div>
              </div>

              <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-200
                                    rounded-lg px-3 py-2">{{ error }}</p>

              <div class="flex gap-3 pt-1">
                <button type="submit" :disabled="guardando"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300
                               text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
                  {{ guardando ? 'Guardando…' : (modoEdicion ? 'Actualizar' : 'Crear menú') }}
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
import { VueDraggable } from 'vue-draggable-plus';
import MenuDragItem from '../../components/admin/MenuDragItem.vue';

// ── Estado ────────────────────────────────────────────────────────────────────
const arbol        = ref([]);
const cargando     = ref(false);
const modalAbierto = ref(false);
const modoEdicion  = ref(false);
const guardando    = ref(false);
const error        = ref('');

// Estado del guardado de orden
const guardandoOrden = ref(false);
const toastVisible   = ref(false);
const toastError     = ref('');
let saveTimer   = null;
let toastTimer  = null;

const form = ref({
  id: null, nombre: '', ruta: '', icono: '',
  menu_id: null, slug: '', orden: 1,
});

// ── Íconos ────────────────────────────────────────────────────────────────────
const iconMap = {
  HomeIcon: '📊', TableCellsIcon: '🪑', ClipboardListIcon: '📋',
  BookOpenIcon: '🍕', FireIcon: '🔥', CurrencyDollarIcon: '💰',
  ArchiveBoxIcon: '📦', UsersIcon: '👥', ChartBarIcon: '📈',
  Cog6ToothIcon: '⚙️', ShieldCheckIcon: '🛡️', UserCircleIcon: '👤',
  Bars3Icon: '☰', KeyIcon: '🔑', LockClosedIcon: '🔒',
};
function resolveIconPreview(ic) { return ic ? (iconMap[ic] ?? ic) : ''; }

// ── Lista plana para el select "menú padre" ───────────────────────────────────
const opcionesPadre = computed(() => {
  const lista = [];
  function recorrer(items, nivel) {
    for (const m of items) {
      lista.push({ id: m.id, nombre: m.nombre, nivel });
      if (m.hijos?.length) recorrer(m.hijos, nivel + 1);
    }
  }
  recorrer(arbol.value, 0);
  return lista;
});

// ── Carga ─────────────────────────────────────────────────────────────────────
async function cargar() {
  cargando.value = true;
  try {
    const { data } = await axios.get('/api/menus');
    arbol.value = data;
  } catch {
    arbol.value = [];
  } finally {
    cargando.value = false;
  }
}

// ── Guardar orden (flatten + POST) ────────────────────────────────────────────
function flattenArbol(items, parentId = null) {
  const result = [];
  items.forEach((item, index) => {
    result.push({ id: item.id, menu_id: parentId, orden: index + 1 });
    if (item.hijos?.length) {
      result.push(...flattenArbol(item.hijos, item.id));
    }
  });
  return result;
}

function scheduleGuardar() {
  clearTimeout(saveTimer);
  saveTimer = setTimeout(guardarOrden, 700);
}

async function guardarOrden() {
  guardandoOrden.value = true;
  toastError.value     = '';
  toastVisible.value   = true;
  clearTimeout(toastTimer);
  try {
    const items = flattenArbol(arbol.value);
    await axios.post('/api/menus/reordenar', { items });
  } catch (e) {
    toastError.value = e.response?.data?.message ?? 'Error al guardar el orden';
  } finally {
    guardandoOrden.value = false;
    // Ocultar el toast después de 2.5 s
    toastTimer = setTimeout(() => { toastVisible.value = false; toastError.value = ''; }, 2500);
  }
}

// ── CRUD modal ────────────────────────────────────────────────────────────────
function abrirCrear() {
  form.value = { id: null, nombre: '', ruta: '', icono: '', menu_id: null, slug: '', orden: 1 };
  modoEdicion.value  = false;
  modalAbierto.value = true;
  error.value        = '';
}

function abrirEditar(item) {
  form.value = {
    id:      item.id,
    nombre:  item.nombre,
    ruta:    item.ruta    ?? '',
    icono:   item.icono   ?? '',
    menu_id: item.menu_id ?? null,
    slug:    item.slug    ?? '',
    orden:   item.orden   ?? 1,
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
      nombre:  form.value.nombre,
      ruta:    form.value.ruta   || null,
      icono:   form.value.icono  || null,
      menu_id: form.value.menu_id,
      slug:    form.value.slug   || null,
      orden:   form.value.orden  ?? 1,
      activo:  true,
    };
    if (modoEdicion.value) {
      await axios.put(`/api/menus/${form.value.id}`, payload);
    } else {
      await axios.post('/api/menus', payload);
    }
    modalAbierto.value = false;
    await cargar();
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

async function confirmarEliminar(item) {
  if (!confirm(`¿Eliminar el menú "${item.nombre}"?`)) return;
  try {
    await axios.delete(`/api/menus/${item.id}`);
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

.toast-enter-active, .toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from, .toast-leave-to       { opacity: 0; transform: translateY(12px); }
</style>
