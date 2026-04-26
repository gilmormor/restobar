<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="stat in stats" :key="stat.label"
        class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
        <div class="text-4xl">{{ stat.icon }}</div>
        <div>
          <p class="text-2xl font-bold text-gray-800">{{ stat.valor }}</p>
          <p class="text-sm text-gray-500">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Bienvenido al sistema Restobar</h3>
      <p class="text-gray-500">Selecciona un módulo del menú para comenzar a gestionar tu restaurante.</p>
    </div>
  </div>
</template>

<script setup>
const stats = [
  { icon: '🪑', label: 'Mesas activas',    valor: '0' },
  { icon: '📋', label: 'Pedidos del día',  valor: '0' },
  { icon: '💰', label: 'Ventas del día',   valor: '$0' },
  { icon: '👥', label: 'Empleados',        valor: '0' },
];
</script>
