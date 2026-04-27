<template>
  <div>
    <div class="mb-6">
      <h2 class="text-xl font-bold text-gray-800">Menú - Rol</h2>
      <p class="text-sm text-gray-500 mt-0.5">Asigna qué menús ve cada rol</p>
    </div>

    <!-- Selector de Rol -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4 flex items-center gap-4">
      <label class="text-sm font-medium text-gray-700 flex-shrink-0">Rol:</label>
      <select v-model="rolSeleccionado" @change="cargarAsignacion"
              class="flex-1 max-w-xs border border-gray-300 rounded-lg px-3 py-2 text-sm
                     focus:outline-none focus:ring-2 focus:ring-orange-300">
        <option :value="null">— Selecciona un rol —</option>
        <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nombre }}</option>
      </select>
      <button v-if="rolSeleccionado" @click="guardar" :disabled="guardando"
              class="bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white
                     text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        {{ guardando ? 'Guardando...' : 'Guardar' }}
      </button>
    </div>

    <!-- Árbol de menús con checkboxes -->
    <div v-if="rolSeleccionado" class="bg-white rounded-xl shadow-sm p-4">
      <div v-if="cargandoMenus" class="text-center text-gray-400 py-8">Cargando...</div>
      <div v-else class="space-y-1">
        <MenuCheckItem
          v-for="item in arbolMenus"
          :key="item.id"
          :item="item"
          :seleccionados="seleccionados"
          :depth="0"
          @toggle="toggleMenu"
        />
      </div>
      <p v-if="mensaje" class="mt-4 text-sm text-green-600 font-medium">{{ mensaje }}</p>
    </div>
    <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
      Selecciona un rol para gestionar sus menús
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MenuCheckItem from '../../components/admin/MenuCheckItem.vue';

const roles          = ref([]);
const arbolMenus     = ref([]);
const rolSeleccionado = ref(null);
const seleccionados  = ref(new Set());
const cargandoMenus  = ref(false);
const guardando      = ref(false);
const mensaje        = ref('');

// Íconos
const iconMap = {
  HomeIcon:'📊', TableCellsIcon:'🪑', ClipboardListIcon:'📋', BookOpenIcon:'🍕',
  FireIcon:'🔥', CurrencyDollarIcon:'💰', ArchiveBoxIcon:'📦', UsersIcon:'👥',
  ChartBarIcon:'📈', Cog6ToothIcon:'⚙️', ShieldCheckIcon:'🛡️',
  UserCircleIcon:'👤', Bars3Icon:'☰', KeyIcon:'🔑', LockClosedIcon:'🔒',
};
function resolveIcon(ic) { return ic ? (iconMap[ic] ?? ic) : null; }

function mapArbol(items) {
  return (items ?? []).map(m => ({
    id:    m.id,
    nombre: m.nombre,
    ruta:  m.ruta,
    icono: resolveIcon(m.icono),
    hijos: mapArbol(m.hijos),
  }));
}

// Cargar roles
async function cargarRoles() {
  const { data } = await axios.get('/api/roles');
  roles.value = data.data ?? data;
}

// Cargar árbol completo de menús
async function cargarArbol() {
  const { data } = await axios.get('/api/menus');
  arbolMenus.value = mapArbol(data);
}

// Cargar menús asignados al rol seleccionado
async function cargarAsignacion() {
  if (!rolSeleccionado.value) return;
  cargandoMenus.value = true;
  mensaje.value = '';
  try {
    const { data } = await axios.get(`/api/roles/${rolSeleccionado.value}`);
    const asignados = data.menus ?? [];
    seleccionados.value = new Set(asignados.map(m => m.id));
  } finally {
    cargandoMenus.value = false;
  }
}

function toggleMenu(id) {
  const s = new Set(seleccionados.value);
  if (s.has(id)) s.delete(id); else s.add(id);
  seleccionados.value = s;
}

async function guardar() {
  guardando.value = true; mensaje.value = '';
  try {
    await axios.put(`/api/roles/${rolSeleccionado.value}/menus`, {
      menus: [...seleccionados.value],
    });
    mensaje.value = 'Menús guardados correctamente';
  } catch {
    mensaje.value = 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

onMounted(async () => {
  await Promise.all([cargarRoles(), cargarArbol()]);
});
</script>
