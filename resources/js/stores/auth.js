import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {

    // ── Estado ────────────────────────────────────────────────────────────────
    const token          = ref(localStorage.getItem('token') || null);
    const usuario        = ref(JSON.parse(localStorage.getItem('usuario') || 'null'));
    const rol            = ref(JSON.parse(localStorage.getItem('rol')     || 'null'));
    const permisos       = ref(JSON.parse(localStorage.getItem('permisos')|| '[]'));
    const sucursalId     = ref(parseInt(localStorage.getItem('sucursal_id') || '0') || null);
    const sucursalNombre = ref(localStorage.getItem('sucursal_nombre') || null);
    const menuItems      = ref([]);

    // ── Computadas ────────────────────────────────────────────────────────────
    const estaAutenticado = computed(() => !!token.value);
    const esSuperadmin    = computed(() => rol.value?.es_superadmin === true);
    const nombreCompleto  = computed(() =>
        usuario.value ? `${usuario.value.nombre} ${usuario.value.apellido}` : ''
    );

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Verifica si el usuario tiene un permiso por slug */
    function puede(slug) {
        if (esSuperadmin.value) return true;
        if (permisos.value.includes('*')) return true;
        return permisos.value.includes(slug);
    }

    function _persistir() {
        localStorage.setItem('token',            token.value || '');
        localStorage.setItem('usuario',          JSON.stringify(usuario.value));
        localStorage.setItem('rol',              JSON.stringify(rol.value));
        localStorage.setItem('permisos',         JSON.stringify(permisos.value));
        localStorage.setItem('sucursal_id',      sucursalId.value || '');
        localStorage.setItem('sucursal_nombre',  sucursalNombre.value || '');
    }

    function _limpiar() {
        token.value          = null;
        usuario.value        = null;
        rol.value            = null;
        permisos.value       = [];
        sucursalId.value     = null;
        sucursalNombre.value = null;
        menuItems.value      = [];
        localStorage.removeItem('token');
        localStorage.removeItem('usuario');
        localStorage.removeItem('rol');
        localStorage.removeItem('permisos');
        localStorage.removeItem('sucursal_id');
        localStorage.removeItem('sucursal_nombre');
    }

    function _setAxiosToken(tkn) {
        if (tkn) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${tkn}`;
            axios.defaults.headers.common['X-Sucursal-Id'] = sucursalId.value || '';
        } else {
            delete axios.defaults.headers.common['Authorization'];
            delete axios.defaults.headers.common['X-Sucursal-Id'];
        }
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    /** Llamar al inicializar la app para restaurar sesión del localStorage */
    function inicializar() {
        if (token.value) {
            _setAxiosToken(token.value);
            cargarMenu(); // fire-and-forget: menú se carga en background al refrescar
        }
    }

    async function login(credenciales) {
        const { data } = await axios.post('/api/auth/login', credenciales);

        token.value      = data.token;
        usuario.value    = data.usuario;
        rol.value        = data.rol;
        permisos.value   = data.permisos;
        sucursalId.value     = data.sucursal_id;
        sucursalNombre.value = data.sucursal_nombre ?? null;

        _persistir();
        _setAxiosToken(data.token);

        // Cargar menú dinámico
        await cargarMenu();

        return data;
    }

    async function logout() {
        try {
            await axios.post('/api/auth/logout');
        } catch (_) {
            // Si falla el logout del server igual limpiamos local
        } finally {
            _limpiar();
            _setAxiosToken(null);
        }
    }

    async function cargarMenu() {
        try {
            const { data } = await axios.get('/api/auth/menu');
            menuItems.value = data.menu;
        } catch (_) {
            menuItems.value = [];
        }
    }

    return {
        // estado
        token, usuario, rol, permisos, sucursalId, sucursalNombre, menuItems,
        // computadas
        estaAutenticado, esSuperadmin, nombreCompleto,
        // acciones
        inicializar, login, logout, cargarMenu, puede,
    };
});
