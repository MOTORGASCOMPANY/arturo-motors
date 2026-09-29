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
        <script src="{{ asset('js/components/carga-swal.js') }}"></script>

        <!-- AppSwal: API unificada SweetAlert2 para toda la aplicación -->
        <script>
            window.AppSwal = {
                cargar: function(titulo, texto) {
                    titulo = titulo || 'Cargando';
                    texto = texto || 'Por favor espera...';
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: function() { Swal.showLoading(); }
                    });
                },

                cerrar: function() {
                    Swal.close();
                },

                exito: function(titulo, texto, opts) {
                    opts = opts || {};
                    return Swal.fire({
                        icon: 'success',
                        title: titulo,
                        text: texto || '',
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: opts.confirmText || 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                error: function(titulo, texto, opts) {
                    opts = opts || {};
                    return Swal.fire({
                        icon: 'error',
                        title: titulo,
                        text: texto || '',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: opts.confirmText || 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                alerta: function(titulo, texto, opts) {
                    opts = opts || {};
                    return Swal.fire({
                        icon: 'warning',
                        title: titulo,
                        text: texto || '',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: opts.confirmText || 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },

                confirmar: function(titulo, texto, opts) {
                    opts = opts || {};
                    return Swal.fire({
                        icon: 'question',
                        title: titulo,
                        text: texto || '',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
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

                toast: function(icono, titulo, posicion) {
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
                    Toast.fire({ icon: icono, title: titulo });
                },

                nota: function(titulo, texto) {
                    return Swal.fire({
                        title: titulo,
                        text: texto || '',
                        icon: 'info',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#2563eb',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            };

            var metodos = ['cargar', 'cerrar', 'exito', 'alerta', 'nota', 'toast'];
            for (var i = 0; i < metodos.length; i++) {
                (function(m) {
                    Livewire.on('swal:' + m, function(data) {
                        var args = Array.isArray(data) ? data : [data];
                        window.AppSwal[m].apply(window.AppSwal, args);
                    });
                })(metodos[i]);
            }

            Livewire.on('swal:confirmar', function(data) {
                var titulo = data.titulo || data.titulo;
                var texto = data.texto || data.mensaje || data.text;
                var opts = {};
                for (var k in data) {
                    if (k !== 'titulo' && k !== 'texto' && k !== 'mensaje' && k !== 'text' && k !== 'onConfirm') {
                        opts[k] = data[k];
                    }
                }
                window.AppSwal.confirmar(titulo, texto, opts).then(function(result) {
                    if (result.isConfirmed && data.onConfirm && typeof data.onConfirm === 'function') {
                        var params = data.callbackParams || [];
                        data.onConfirm.apply(null, params);
                    }
                });
            });

            Livewire.on('swal:input', function(data) {
                var titulo = data.titulo || data.titulo;
                var tipo = data.tipo || 'text';
                var opts = {};
                for (var k in data) {
                    if (k !== 'titulo' && k !== 'tipo' && k !== 'onConfirm') {
                        opts[k] = data[k];
                    }
                }
                window.AppSwal.input(titulo, tipo, opts).then(function(result) {
                    if (result.isConfirmed && data.onConfirm && typeof data.onConfirm === 'function') {
                        var params = { value: result.value };
                        if (data.callbackParams) {
                            for (var pk in data.callbackParams) {
                                params[pk] = data.callbackParams[pk];
                            }
                        }
                        data.onConfirm(params);
                    }
                });
            });

            Livewire.on('swal', function(data) {
                if (data && data.icon && !data.custom) {
                    Swal.fire(data);
                }
            });

            // Eventos existentes de componentes
            Livewire.on('asignacion-bloqueada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.alerta(d.titulo || 'Atención', d.mensaje || '');
            });
            Livewire.on('asignacion-confirmar', function() {
                window.AppSwal.confirmar('¿Confirmar asignación?', 'Esta acción no se puede deshacer')
                    .then(function(r) { if (r.isConfirmed) Livewire.dispatch('confirmar-entrega'); });
            });
            Livewire.on('entrega-confirmada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.exito('¡Componentes asignados!', 'La asignación se confirmó correctamente.')
                    .then(function() { if (d && d.redirectUrl) window.location.href = d.redirectUrl; });
            });
            Livewire.on('entrega-error', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.error('Error', d.mensaje || 'Ocurrió un error');
            });
            Livewire.on('conversion-terminada', function(data) {
                var d = Array.isArray(data) ? data[0] : data;
                window.AppSwal.exito('¡Conversión terminada!', 'La orden está lista para entrega.')
                    .then(function() { if (d && d.redirectUrl) window.location.href = d.redirectUrl; });
            });
            Livewire.on('registrarPago', function() {
                window.AppSwal.cargar('Registrando pago', 'Procesando...');
            });
            Livewire.on('swal-init', function(data) {});
            Livewire.on('swal-kit', function(data) {});
            Livewire.on('minToast', function(data) {
                var icono = data.icono || data.icon || 'success';
                var titulo = data.titulo || data.title || '';
                var texto = data.mensaje || data.text || '';
                window.AppSwal.toast(icono, (titulo ? titulo + ': ' : '') + texto);
            });
            Livewire.on('minAlert', function(data) {
                var icono = data.icono || data.icon || 'warning';
                var titulo = data.titulo || data.title || 'Atención';
                var texto = data.mensaje || data.text || '';
                window.AppSwal.alerta(titulo, texto);
            });

            @if (session()->has('swal'))
                (function() {
                    var swalData = @json(session('swal'));
                    var Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: function(toast) {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({
                        icon: swalData.icono || swalData.icon || 'success',
                        title: swalData.titulo || swalData.title || '',
                        text: swalData.mensaje || swalData.text || ''
                    });
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
