/**
 * CargaSwal — componente reutilizable de carga (SweetAlert2) para reportes.
 *
 * Un único punto de implementación: los reportes NO replican el bloque de
 * Swal.fire(...didOpen: () => Swal.showLoading()...), solo llaman a este API.
 *
 * Uso:
 *   CargaSwal.cargar('Exportando PDF', 'Generando el reporte...');  // popup de carga
 *   CargaSwal.cerrar();
 *   CargaSwal.exito('Descarga iniciada', 'El archivo PDF se está descargando.');
 *   CargaSwal.alerta('Sin datos', 'No hay datos para exportar en este período.');
 *   CargaSwal.exportar({ url, titulo: 'Exportando PDF', texto: 'Generando...', archivo: 'PDF' });
 */
(function () {
    'use strict';

    /** Apariencia única del popup en todos los reportes. */
    var CLASE_POPUP = { popup: 'rounded-2xl' };

    function swalListo() {
        return typeof Swal !== 'undefined';
    }

    function popupCarga(titulo, texto) {
        return {
            title: titulo || 'Cargando',
            text: texto || 'Un momento por favor...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            customClass: CLASE_POPUP
        };
    }

    window.CargaSwal = {
        /** Popup de carga con el spinner de SweetAlert2 (no se cierra con click ni Esc). */
        cargar: function (titulo, texto) {
            if (!swalListo()) return null;
            var opciones = popupCarga(titulo, texto);
            opciones.didOpen = function () {
                Swal.showLoading();
            };
            return Swal.fire(opciones);
        },

        /** Cierra el popup de carga. */
        cerrar: function () {
            if (swalListo()) Swal.close();
        },

        /** Éxito breve (toast arriba a la derecha). */
        exito: function (titulo, texto) {
            if (!swalListo()) return;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: titulo || 'Listo',
                text: texto || '',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                customClass: CLASE_POPUP
            });
        },

        /** Aviso (p. ej. "Sin datos"). */
        alerta: function (titulo, texto) {
            if (!swalListo()) return;
            Swal.fire({
                title: titulo || 'Sin datos',
                text: texto || '',
                icon: 'warning',
                timer: 3000,
                showConfirmButton: false,
                customClass: CLASE_POPUP
            });
        },

        /** Nota breve informativa (icono info, se cierra sola). */
        nota: function (titulo, texto) {
            if (!swalListo()) return;
            Swal.fire({
                title: titulo || 'Aviso',
                text: texto || '',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false,
                customClass: CLASE_POPUP
            });
        },

        /**
         * Descarga de export (PDF/Excel): carga -> navega -> cierra -> confirma.
         * @param {{url: string, titulo?: string, texto?: string, archivo?: string, nuevaVentana?: boolean, okTitulo?: string, okTexto?: string}} opciones
         *   nuevaVentana: abre la exportación en otra pestaña (los botones con target="_blank").
         */
        exportar: function (opciones) {
            var o = opciones || {};

            if (!o.url || o.url === '#') {
                this.alerta('Sin datos', 'No hay datos para exportar en este período.');
                return;
            }

            // Sin SweetAlert2 disponible: se navega igual (fallback).
            if (!swalListo()) {
                if (o.nuevaVentana) { window.open(o.url, '_blank'); } else { window.location.href = o.url; }
                return;
            }

            var okTexto = o.okTexto || (o.archivo
                ? 'El archivo ' + o.archivo + ' se está descargando.'
                : 'El archivo se está descargando.');

            this.cargar(o.titulo, o.texto);
            if (o.nuevaVentana) {
                window.open(o.url, '_blank');
            } else {
                window.location.href = o.url;
            }

            var yo = this;
            setTimeout(function () {
                yo.cerrar();
                yo.exito('Descarga iniciada', okTexto);
            }, 3000);
        }
    };
})();
