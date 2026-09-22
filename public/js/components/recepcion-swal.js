/**
 * recepcionSwal — SweetAlert2 helpers for the recepción workflow.
 *
 * Usage: recepcionSwal.alerta({ tipo, titulo, mensaje })
 *        recepcionSwal.accionConfirmada($wire, dataset)
 *        recepcionSwal.validarYLlamar($wire, campos, metodo)
 *        etc.
 *
 * Depends on: SweetAlert2 (Swal), Livewire
 */
window.recepcionSwal = window.recepcionSwal || (function () {
    const base = {
        confirmButtonColor: '#4F46E5',
        cancelButtonColor: '#6B7280',
        denyButtonColor: '#D97706',
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        reverseButtons: true,
    };

    const construirLista = (items) => {
        const ul = document.createElement('ul');
        ul.style.cssText = 'text-align:left;margin:.5rem auto 0;max-width:24rem;list-style:disc;padding-left:1.25rem;font-size:.9rem;';
        items.forEach((t) => {
            const li = document.createElement('li');
            li.textContent = t;
            ul.appendChild(li);
        });
        return ul;
    };

    const alerta = ({ tipo = 'info', titulo = '', mensaje = '', lista = [], boton = 'OK' } = {}) => {
        const opts = { ...base, icon: tipo, title: titulo, confirmButtonText: boton };
        if (lista.length) {
            const cont = document.createElement('div');
            if (mensaje) {
                const p = document.createElement('p');
                p.textContent = mensaje;
                cont.appendChild(p);
            }
            cont.appendChild(construirLista(lista));
            opts.html = cont;
        } else if (mensaje) {
            opts.text = mensaje;
        }
        return Swal.fire(opts);
    };

    const confirmar = async ({ titulo, texto = '', boton = 'Sí, continuar', tipo = 'question' }) => {
        const r = await Swal.fire({ ...base, icon: tipo, title: titulo, text: texto, showCancelButton: true, confirmButtonText: boton });
        return r.isConfirmed;
    };

    const desdeServidor = async (d, $wire) => {
        d = Array.isArray(d) ? (d[0] || {}) : (d || {});
        await alerta({
            tipo: d.tipo || 'info',
            titulo: d.titulo || '',
            mensaje: d.mensaje || '',
            lista: Array.isArray(d.lista) ? d.lista : [],
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
        const html = document.createElement('div');
        const p = document.createElement('p');
        p.textContent = `Faltan ${vacios.length} serie(s) por llenar:`;
        html.appendChild(p);
        html.appendChild(construirLista(items));

        const r = await Swal.fire({
            ...base,
            icon: 'warning',
            title: 'Hay series sin llenar',
            html,
            showDenyButton: true,
            confirmButtonText: 'Completar series',
            denyButtonText: 'Continuar sin serie',
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
        const campos = [['nuevoNombre', 'El nombre del producto']];
        if ($wire.nuevoTipo === 'serializado') campos.push(['nuevoCategoriaId', 'La categoría']);
        await validarYLlamar($wire, campos, 'registrarComponenteNuevo');
    };

    const elegir = async ($wire, destino) => {
        await $wire.elegirSeccion(destino);
    };

    const editarKit = async ($wire, ds) => {
        const r = await Swal.fire({
            ...base,
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
                    Swal.showValidationMessage('El nombre del kit es obligatorio');
                    return false;
                }
                return { nombre, gen };
            },
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

    const engancharErrores = () => {
        if (window.__recepcionSwalHook || !window.Livewire || typeof Livewire.hook !== 'function') return;
        window.__recepcionSwalHook = true;
        Livewire.hook('request', (ctx) => {
            if (!ctx || typeof ctx.fail !== 'function') return;
            ctx.fail(({ status, content, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    alerta({ tipo: 'warning', titulo: 'Tu sesión expiró', mensaje: 'Recarga la página para continuar.', boton: 'Recargar' }).then(() => location.reload());
                } else if (status >= 500) {
                    preventDefault();
                    console.error('Error de servidor en Livewire:', content);
                    alerta({ tipo: 'error', titulo: 'No se pudo completar la acción', mensaje: 'Ocurrió un error inesperado. Presiona OK y vuelve a intentarlo.' });
                }
            });
        });
    };
    if (window.Livewire) engancharErrores();
    else document.addEventListener('livewire:init', engancharErrores);

    return {
        alerta, confirmar, desdeServidor, accionConfirmada, validarYLlamar, registrarComponenteNuevo,
        elegir, editarKit, eliminarKit, quitarComponente, cancelarComponentes,
    };
})();
