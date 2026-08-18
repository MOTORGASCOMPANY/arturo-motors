<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
        <title>ARTURO MOTORS</title>
        <!-- Este es el app.blade.php de components/layouts -->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        {{-- Agregue esto para date-picker --}}
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        
        <!-- Scripts -->
        

        <!-- Styles -->
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>


    <body>
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            {{-- @livewire('navigation-menu') --}}
            @livewire('custom-nav-menu')

            <!-- Page Content -->
            <main class="pt-16">
                {{ $slot }}
            </main>
        </div>

        {{-- $slot --}}
        @stack('modals')

        @livewireScripts

        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Script para SweetAlert2 con Livewire -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                // 1. Notificaciones enviadas por redirección con session()->flash('swal', [...])
                @if (session()->has('swal'))
                    (function() {
                        const swalData = @json(session('swal'));
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: swalData.icono || swalData.icon || 'success',
                            title: swalData.titulo || swalData.title || '',
                            text: swalData.mensaje || swalData.text || '',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    })();
                @endif

                // 2. Alerta Modal Centrada vía Livewire dispatch (sin redirección)
                Livewire.on('minAlert', function(params) {
                    Swal.fire({
                        title: params.titulo || params['titulo'],
                        text: params.mensaje || params['mensaje'],
                        icon: params.icono || params['icono']
                    });
                });

                // 3. Toast en tiempo real vía Livewire dispatch (sin redirección)
                Livewire.on('minToast', function(params) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: params.icono || params['icono'],
                        title: params.titulo || params['titulo'],
                        text: params.mensaje || params['mensaje'],
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            });
        </script>

        @stack('js')


        <footer>
            <div class="text-xs text-slate-700  float-right">
                Powered by GHFDEV ®
            </div>
        </footer>
    </body>

</html>
