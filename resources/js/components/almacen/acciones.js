window.almacenAcciones = window.almacenAcciones || (function () {
    const app = () => window.AppSwal;

    const alerta = ({ tipo = 'info', titulo = '', mensaje = '', lista = [], boton = 'OK' } = {}) =>
        app().aviso(tipo, titulo, mensaje, { lista: lista, confirmText: boton });

    const confirmar = async ({ titulo, texto = '', boton = 'Sí, continuar', tipo = 'question' }) => {
        const r = await app().confirmar(titulo, texto, { icon: tipo, confirmText: boton });
        return r.isConfirmed;
    };

    const desdeServidor = async (d, $wire) => {
        d = Array.isArray(d) ? (d[0] || {}) : (d || {});
        await alerta({
            tipo: d.tipo || 'info',
            titulo: d.titulo || '',
            mensaje: d.mensaje || '',
            lista: Array.isArray(d.lista) ? d.lista : []
        });
        if (d.continuar && $wire) await $wire[d.continuar]();
    };

    const revisarSeries = async (contenedorId) => {
        const cont = document.getElementById(contenedorId);
        if (!cont) return true;
        const vacios = [...cont.querySelectorAll('input[data-serie]')].filter((i) => !i.value.trim());
        if (!vacios.length) return true;

        const items = vacios.slice(0, 8).map((i) => i.dataset.etiqueta || 'Serie sin nombre');
        if (vacios.length > 8) items.push(`… y ${vacios.length - 8} más`);

        const r = await app().formulario({
            icon: 'warning',
            title: 'Hay series sin llenar',
            html: app().htmlConLista(`Faltan ${vacios.length} serie(s) por llenar:`, items),
            showDenyButton: true,
            confirmButtonText: 'Completar series',
            denyButtonText: 'Continuar sin serie'
        });
        if (r.isConfirmed) {
            const primero = vacios[0];
            primero.scrollIntoView({ behavior: 'smooth', block: 'center' });
            primero.focus();
            return false;
        }
        return r.isDenied;
    };

    const accionConfirmada = async ($wire, ds) => {
        if (ds.total !== undefined && (parseInt(ds.total, 10) || 0) < 1) {
            return alerta({ tipo: 'warning', titulo: 'Falta la cantidad', mensaje: ds.aviso || 'Indica al menos una unidad para continuar.' });
        }
        if (ds.contenedor && !(await revisarSeries(ds.contenedor))) return;
        const ok = await confirmar({ titulo: ds.titulo || '¿Continuar?', texto: ds.texto || '', boton: ds.boton || 'Sí, continuar' });
        if (ok) await $wire[ds.metodo]();
    };

    const validarYLlamar = async ($wire, campos, metodo) => {
        const faltan = campos
            .filter(([prop]) => {
                const v = $wire[prop];
                return v === null || v === undefined || String(v).trim() === '';
            })
            .map(([, etiqueta]) => `${etiqueta} es obligatorio`);
        if (faltan.length) {
            return alerta({ tipo: 'warning', titulo: 'Faltan datos', mensaje: 'Completa lo siguiente y presiona OK para continuar:', lista: faltan });
        }
        await $wire[metodo]();
    };

    const registrarComponenteNuevo = async ($wire) => {
        const campos = $wire.nuevoTipo === 'serializado'
            ? [['nuevoCategoriaId', 'La categoría']]
            : [['nuevoNombre', 'El nombre del producto']];
        await validarYLlamar($wire, campos, 'registrarComponenteNuevo');
    };

    const elegir = async ($wire, destino) => {
        await $wire.elegirSeccion(destino);
    };

    const editarKit = async ($wire, ds) => {
        const r = await app().formulario({
            title: 'Editar kit',
            html:
                '<div style="text-align:left">' +
                '<label for="swal-kit-nombre" style="font-size:.85rem;font-weight:600">Nombre del kit</label>' +
                '<input id="swal-kit-nombre" class="swal2-input" style="margin:.25rem 0 1rem;width:100%" maxlength="100" autocomplete="off">' +
                '<label for="swal-kit-gen" style="font-size:.85rem;font-weight:600">Generación</label>' +
                '<input id="swal-kit-gen" class="swal2-input" style="margin:.25rem 0 0;width:100%" maxlength="50" placeholder="Ej: 3ra Generación" autocomplete="off">' +
                '</div>',
            showCancelButton: true,
            confirmButtonText: 'Guardar cambios',
            didOpen: () => {
                document.getElementById('swal-kit-nombre').value = ds.nombre || '';
                document.getElementById('swal-kit-gen').value = ds.gen || '';
            },
            preConfirm: () => {
                const nombre = document.getElementById('swal-kit-nombre').value.trim();
                const gen = document.getElementById('swal-kit-gen').value.trim();
                if (!nombre) {
                    app().validacion('El nombre del kit es obligatorio');
                    return false;
                }
                return { nombre, gen };
            }
        });
        if (r.isConfirmed) await $wire.actualizarKit(Number(ds.id), r.value.nombre, r.value.gen);
    };

    const eliminarKit = async ($wire, ds) => {
        const ok = await confirmar({ tipo: 'warning', titulo: '¿Desactivar este kit?', texto: `Se desactivará "${ds.nombre}".`, boton: 'Sí, desactivar' });
        if (ok) await $wire.eliminarKit(Number(ds.id));
    };

    const quitarComponente = async ($wire, ds) => {
        const ok = await confirmar({ tipo: 'warning', titulo: '¿Quitar componente?', texto: `Se quitará "${ds.nombre}" de este kit.`, boton: 'Sí, quitar' });
        if (ok) await $wire.quitarComponenteModal(Number(ds.idx));
    };

    const cancelarComponentes = async ($wire) => {
        const ok = await confirmar({ tipo: 'warning', titulo: '¿Salir del registro de componentes?', texto: 'Se perderá lo capturado en este paso.', boton: 'Sí, salir' });
        if (ok) await $wire.cerrarModal();
    };

    return {
        desdeServidor, accionConfirmada, validarYLlamar, registrarComponenteNuevo,
        elegir, editarKit, eliminarKit, quitarComponente, cancelarComponentes
    };
})();
