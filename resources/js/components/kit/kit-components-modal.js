document.addEventListener('alpine:init', () => {
    Alpine.data('kitComponentsModal', () => ({
        abierto: false,
        productoId: null,
        filtroEstado: null,
        producto: null,
        receta: [],
        kits: [],
        kitsFiltrados: [],
        cargando: false,

        meta: {
            en_stock:   { label: 'Sellado',    chip: 'bg-green-100 text-green-700',   box: 'bg-green-100',   icon: 'fa-box text-green-600' },
            abierto:    { label: 'Abierto',    chip: 'bg-orange-100 text-orange-700', box: 'bg-orange-100',  icon: 'fa-folder-open text-orange-600' },
            completado: { label: 'Completado', chip: 'bg-purple-100 text-purple-700', box: 'bg-purple-100',  icon: 'fa-check-circle text-purple-600' },
            asignado:   { label: 'Asignado',   chip: 'bg-yellow-100 text-yellow-700', box: 'bg-yellow-100',  icon: 'fa-user-check text-yellow-600' },
            instalado:  { label: 'Instalado',  chip: 'bg-blue-100 text-blue-700',     box: 'bg-blue-100',    icon: 'fa-wrench text-blue-600' },
            consumido:  { label: 'Consumido',  chip: 'bg-red-100 text-red-700',       box: 'bg-red-100',     icon: 'fa-fire text-red-600' }
        },

        info(estado) {
            return this.meta[estado] || { label: estado, chip: 'bg-gray-100 text-gray-600', box: 'bg-gray-100', icon: 'fa-circle text-gray-500' };
        },

        async abrir(productoId, filtroEstado = null) {
            this.productoId = productoId;
            this.filtroEstado = filtroEstado;
            this.abierto = true;
            await this.cargarComponentes();
        },

        async cargarComponentes() {
            if (!this.productoId) return;
            this.cargando = true;
            this.kits = [];
            this.receta = [];
            try {
                const resp = await fetch(`/api/kit-componentes/${this.productoId}`);
                const data = await resp.json();
                this.producto = data.producto;
                this.receta = data.receta || [];
                this.kits = (data.kits || []).map(k => ({ ...k, _open: false }));
                this.aplicarFiltro();
            } catch (e) {
                console.error('Error cargando kit:', e);
            } finally {
                this.cargando = false;
            }
        },

        aplicarFiltro() {
            if (!this.filtroEstado) {
                this.kitsFiltrados = this.kits;
            } else {
                this.kitsFiltrados = this.kits.filter(k => k.estado === this.filtroEstado);
            }
            if (this.kitsFiltrados.length === 1) {
                this.kitsFiltrados[0]._open = true;
            }
        },

        contarPorEstado(estado) {
            return this.kits.filter(k => k.estado === estado).length;
        },

        cerrar() {
            this.abierto = false;
            this.filtroEstado = null;
        }
    }));
});
