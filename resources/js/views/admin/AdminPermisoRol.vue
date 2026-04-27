<template>
  <div>
    <div class="mb-6">
      <h2 class="text-xl font-bold text-gray-800">Permiso - Rol</h2>
      <p class="text-sm text-gray-500 mt-0.5">Asigna qué permisos tiene cada rol</p>
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

    <!-- Tabla de permisos con checkboxes, agrupados por slug prefix -->
    <div v-if="rolSeleccionado" class="bg-white rounded-xl shadow-sm p-4">
      <div v-if="cargando" class="text-center text-gray-400 py-8">Cargando...</div>
      <div v-else>
        <!-- Buscador -->
        <input v-model="busqueda" type="text" placeholder="Filtrar permisos..."
               class="w-full mb-4 border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-orange-300" />

        <!-- Grupos de permisos -->
        <div v-for="(grupo, prefix) in gruposPermiso" :key="prefix" class="mb-4">
          <div class="flex items-center gap-2 mb-2">
            <button @click="toggleGrupo(grupo)"
                    class="text-xs text-orange-500 hover:underline">
              Todo
            </button>
            <span class="text-sm font-semibold text-gray-600 capitalize">{{ prefix }}</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
            <label v-for="p in grupo" :key="p.id"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
              <input type="checkbox"
                     :checked="seleccionados.has(p.id)"
                     @change="togglePermiso(p.id)"
                     class="w-4 h-4 accent-orange-500 rounded" />
              <span class="text-sm text-gray-700">{{ p.nombre }}</span>
            </label>
          </div>
        </div>

        <p v-if="mensaje" class="mt-2 text-sm text-green-600 font-medium">{{ mensaje }}</p>
      </div>
    </div>
    <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
      Selecciona un rol para gestionar sus permisos
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const roles          = ref([]);
const permisosTodos  = ref([]);
const rolSeleccionado = ref(null);
const seleccionados  = ref(new Set());
const busqueda       = ref('');
const cargando       = ref(false);
const guardando      = ref(false);
const mensaje        = ref('');

const permisosFiltrados = computed(() => {
  const q = busqueda.value.toLowerCase();
  if (!q) return permisosTodos.value;
  return permisosTodos.value.filter(p =>
    p.slug?.toLowerCase().includes(q) || p.nombre?.toLowerCase().includes(q)
  );
});

// Agrupar por prefijo del slug (ej: "mesas", "pedidos")
const gruposPermiso = computed(() => {
  const grupos = {};
  for (const p of permisosFiltrados.value) {
    const prefix = p.slug?.split('.')[0] ?? 'general';
    if (!grupos[prefix]) grupos[prefix] = [];
    grupos[prefix].push(p);
  }
  return grupos;
});

async function cargarRoles() {
  const { data } = await axios.get('/api/roles');
  roles.value = data.data ?? data;
}

async function cargarPermisosTodos() {
  const { data } = await axios.get('/api/permisos');
  permisosTodos.value = data.data ?? data;
}

async function cargarAsignacion() {
  if (!rolSeleccionado.value) return;
  cargando.value = true; mensaje.value = '';
  try {
    const { data } = await axios.get(`/api/roles/${rolSeleccionado.value}`);
    const asignados = data.permisos ?? [];
    seleccionados.value = new Set(asignados.map(p => p.id));
  } finally {
    cargando.value = false;
  }
}

function togglePermiso(id) {
  const s = new Set(seleccionados.value);
  if (s.has(id)) s.delete(id); else s.add(id);
  seleccionados.value = s;
}

function toggleGrupo(grupo) {
  const s = new Set(seleccionados.value);
  const todosActivos = grupo.every(p => s.has(p.id));
  if (todosActivos) {
    grupo.forEach(p => s.delete(p.id));
  } else {
    grupo.forEach(p => s.add(p.id));
  }
  seleccionados.value = s;
}

async function guardar() {
  guardando.value = true; mensaje.value = '';
  try {
    await axios.put(`/api/roles/${rolSeleccionado.value}/permisos`, {
      permisos: [...seleccionados.value],
    });
    mensaje.value = 'Permisos guardados correctamente';
  } catch {
    mensaje.value = 'Error al guardar';
  } finally {
    guardando.value = false;
  }
}

onMounted(() => Promise.all([cargarRoles(), cargarPermisosTodos()]));
</script>
