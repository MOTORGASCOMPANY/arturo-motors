<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/jpg" href="{{ asset('images/LOGOFINAL.jpg') }}" />
        <title>ARTURO MOTORS</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>


    <body>
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('custom-nav-menu')

            <main class="pt-16">
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Alertas SweetAlert2 de la aplicación -->
        <script>
            // Alerta de carga: spinner de SweetAlert2 (exportaciones).
            window.AppSwal = {
                cargar: function(titulo, texto) {
                    titulo = titulo || 'Cargando';
                    texto = texto || 'Por favor espera...';
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' },
                        didOpen: function() { Swal.showLoading(); }
                    });
                },

                cerrar: function() {
                    Swal.close();
                },

                aviso: function(tipo, titulo, texto, opts) {
                    opts = opts || {};
                    var conLista = !!(opts.lista && opts.lista.length);
                    var colores = { success: '#16a34a', error: '#dc2626', warning: '#f59e0b', info: '#2563eb' };
                    return Swal.fire({
                        icon: tipo,
                        title: titulo,
                        text: conLista ? '' : (texto || ''),
                        html: conLista ? window.AppSwal.htmlConLista(texto, opts.lista) : undefined,
                        confirmButtonColor: opts.confirmButtonColor || colores[tipo],
                        confirmButtonText: opts.confirmText || (tipo === 'info' ? 'Entendido' : 'OK'),
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                // Alerta de éxito.
                exito: function(titulo, texto, opts) {
                    return window.AppSwal.aviso('success', titulo, texto, opts);
                },

                // Alerta de error.
                error: function(titulo, texto, opts) {
                    return window.AppSwal.aviso('error', titulo, texto, opts);
                },

                // Alerta de advertencia.
                alerta: function(titulo, texto, opts) {
                    return window.AppSwal.aviso('warning', titulo, texto, opts);
                },

                // Alerta de confirmación.
                confirmar: function(titulo, texto, opts) {
                    opts = opts || {};
                    return Swal.fire({
                        icon: opts.icon || 'question',
                        title: titulo,
                        text: texto || '',
                        showCancelButton: true,
                        confirmButtonColor: opts.confirmButtonColor || '#16a34a',
                        cancelButtonColor: opts.cancelButtonColor || '#6b7280',
                        confirmButtonText: opts.confirmText || 'Sí, confirmar',
                        cancelButtonText: opts.cancelText || 'Cancelar',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                input: function(titulo, tipo, opts) {
                    opts = opts || {};
                    tipo = tipo || 'text';
                    return Swal.fire({
                        title: titulo,
                        input: tipo,
                        inputPlaceholder: opts.placeholder,
                        inputValue: opts.value,
                        showCancelButton: true,
                        confirmButtonText: opts.confirmText || 'Guardar',
                        cancelButtonText: 'Cancelar',
                        inputValidator: opts.validator,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                toast: function(icono, titulo, posicion, texto) {
                    posicion = posicion || 'top-end';
                    var Toast = Swal.mixin({
                        toast: true,
                        position: posicion,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: function(toast) {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({ icon: icono, title: titulo, text: texto || '' });
                },

                // Alerta informativa.
                nota: function(titulo, texto, opts) {
                    return window.AppSwal.aviso('info', titulo, texto, opts);
                },

              
                // Alerta de exportación: spinner y toast de descarga.
                exportar: function(o) {
                    o = o || {};

                    if (!o.url || o.url === '#') {
                        return window.AppSwal.alerta('Sin datos', 'No hay datos para exportar en este período.');
                    }

                    if (typeof Swal === 'undefined') {
                        if (o.nuevaVentana) { window.open(o.url, '_blank'); } else { window.location.href = o.url; }
                        return;
                    }

                    var okTexto = o.okTexto || (o.archivo
                        ? 'El archivo ' + o.archivo + ' se está descargando.'
                        : 'El archivo se está descargando.');

                    window.AppSwal.cargar(o.titulo, o.texto);
                    if (o.nuevaVentana) {
                        window.open(o.url, '_blank');
                    } else {
                        window.location.href = o.url;
                    }

                    setTimeout(function() {
                        window.AppSwal.cerrar();
                        window.AppSwal.toast('success', (o.okTitulo || 'Descarga iniciada') + ': ' + okTexto);
                    }, 3000);
                }
            };


            window.AppSwal.htmlConLista = function(texto, items) {
                var cont = document.createElement('div');
                if (texto) {
                    var p = document.createElement('p');
                    p.textContent = texto;
                    cont.appendChild(p);
                }
                var ul = document.createElement('ul');
                ul.style.cssText = 'text-align:left;margin:.5rem auto 0;max-width:24rem;list-style:disc;padding-left:1.25rem;font-size:.9rem;';
                (items || []).forEach(function(t) {
                    var li = document.createElement('li');
                    li.textContent = t;
                    ul.appendChild(li);
                });
                cont.appendChild(ul);
                return cont.outerHTML;
            };

            window.AppSwal.formulario = function(cfg) {
                return Swal.fire(Object.assign({
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    denyButtonColor: '#f59e0b',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'OK',
                    customClass: { popup: 'rounded-2xl' }
                }, cfg || {}));
            };

            window.AppSwal.validacion = function(msg) {
                Swal.showValidationMessage(msg);
            };

            window.AppSwal.engancharErrores = function() {
                if (window.__appSwalErroresHook || !window.Livewire || typeof Livewire.hook !== 'function') return;
                window.__appSwalErroresHook = true;
                Livewire.hook('request', function(ctx) {
                    if (!ctx || typeof ctx.fail !== 'function') return;
                    ctx.fail(function(r) {
                        if (r.status === 419) {
                            r.preventDefault();
                            window.AppSwal.alerta('Tu sesión expiró', 'Recarga la página para continuar.', { confirmText: 'Recargar' })
                                .then(function() { location.reload(); });
                        } else if (r.status >= 500) {
                            r.preventDefault();
                            console.error('Error de servidor en Livewire:', r.content);
                            window.AppSwal.error('No se pudo completar la acción', 'Ocurrió un error inesperado. Presiona OK y vuelve a intentarlo.');
                        }
                    });
                });
            };
            if (window.Livewire) window.AppSwal.engancharErrores();
            else document.addEventListener('livewire:init', function() { window.AppSwal.engancharErrores(); });

            // Alerta desde payload { tipo, titulo, mensaje }.
            window.AppSwal.mostrar = function(data) {
                data = data || {};
                var icono = data.tipo || data.icono || data.icon || 'warning';
                var titulo = data.titulo || data.title || 'Atención';
                var texto = data.mensaje || data.text || '';
                if (icono === 'success') return window.AppSwal.exito(titulo, texto, data);
                if (icono === 'error') return window.AppSwal.error(titulo, texto, data);
                if (icono === 'info') return window.AppSwal.nota(titulo, texto, data);
                return window.AppSwal.alerta(titulo, texto, data);
            };

            // Alerta de swal:<método> → AppSwal.<método>(...).
            var metodos = ['cargar', 'cerrar', 'exito', 'error', 'alerta', 'nota', 'toast'];
            for (var i = 0; i < metodos.length; i++) {
                (function(m) {
                    Livewire.on('swal:' + m, function(data) {
                        if (Array.isArray(data)) return window.AppSwal[m].apply(window.AppSwal, data);
                        if (data && typeof data === 'object') {
                            return window.AppSwal[m](data.titulo, data.mensaje, data);
                        }
                        window.AppSwal[m](data);
                    });
                })(metodos[i]);
            }

            // Alertas de minAlert, swal-kit, swal-init y swal.
            ['minAlert', 'swal-kit', 'swal-init', 'swal'].forEach(function(ev) {
                Livewire.on(ev, function(data) {
                    window.AppSwal.mostrar(Array.isArray(data) ? data[0] : data);
                });
            });

            Livewire.on('swal:confirmar', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                var opts = {};
                for (var k in d) {
                    if (k !== 'titulo' && k !== 'texto' && k !== 'mensaje' && k !== 'text' &&
                        k !== 'onConfirm' && k !== 'callbackParams') {
                        opts[k] = d[k];
                    }
                }
                window.AppSwal.confirmar(d.titulo, d.mensaje || d.texto || d.text, opts)
                    .then(function(result) {
                        if (result.isConfirmed && d.onConfirm && typeof d.onConfirm === 'function') {
                            d.onConfirm.apply(null, d.callbackParams || []);
                        }
                    });
            });

            Livewire.on('swal:input', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                var opts = {};
                for (var k in d) {
                    if (k !== 'titulo' && k !== 'tipo' && k !== 'onConfirm') {
                        opts[k] = d[k];
                    }
                }
                window.AppSwal.input(d.titulo, d.tipo || 'text', opts).then(function(result) {
                    if (result.isConfirmed && d.onConfirm && typeof d.onConfirm === 'function') {
                        var params = { value: result.value };
                        if (d.callbackParams) {
                            for (var pk in d.callbackParams) {
                                params[pk] = d.callbackParams[pk];
                            }
                        }
                        d.onConfirm(params);
                    }
                });
            });

            // Eventos que solo orquestan el flujo (redirect, siguiente evento).

            Livewire.on('asignacion-bloqueada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.mostrar({ tipo: 'warning', titulo: d.titulo, mensaje: d.mensaje });
            });

            Livewire.on('asignacion-confirmar', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.confirmar(d.titulo, d.mensaje)
                    .then(function(r) { if (r.isConfirmed) Livewire.dispatch('confirmar-entrega'); });
            });

            // Confirmación de traslado con kit incompleto.
            Livewire.on('traslado-kit-incompleto', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.confirmar(d.titulo, d.mensaje, d.opts)
                    .then(function(r) {
                        if (r.isConfirmed) Livewire.dispatch('traslado-kit-incompleto-ok', { kitId: d.kitId });
                    });
            });

            Livewire.on('entrega-confirmada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.exito(d.titulo, d.mensaje)
                    .then(function() { if (d.redirectUrl) window.location.href = d.redirectUrl; });
            });

            Livewire.on('entrega-error', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.error(d.titulo || 'Error', d.mensaje);
            });

            Livewire.on('conversion-terminada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.exito(d.titulo, d.mensaje)
                    .then(function() { if (d.redirectUrl) window.location.href = d.redirectUrl; });
            });

            // Alerta de carga durante el pago.
            Livewire.on('registrarPago', function() {
                window.AppSwal.cargar('Registrando pago', 'Procesando...');
            });

            Livewire.on('minToast', function(data) {
                var icono = data.icono || data.icon || 'success';
                var titulo = data.titulo || data.title || '';
                var texto = data.mensaje || data.text || '';
                window.AppSwal.toast(icono, (titulo ? titulo + ': ' : '') + texto);
            });

            @if (session()->has('swal'))
                (function() {
                    var swalData = @json(session('swal'));
                    window.AppSwal.toast(
                        swalData.icono || swalData.icon || 'success',
                        swalData.titulo || swalData.title || '',
                        'top-end',
                        swalData.mensaje || swalData.text || ''
                    );
                })();
            @endif
        </script>

        @stack('js')


        <footer>
            <div class="text-xs text-slate-700  float-right">
                Powered by GHFDEV ®
            </div>
        </footer>
    </body>

</html>
