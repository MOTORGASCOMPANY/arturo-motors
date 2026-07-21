<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <!--x-application-logo class="block h-12 w-auto" /-->
    <div class="text-2xl">
        Hola, {{ Auth::user()->name }}
        <span> </span>
    </div>

    <!-- Métricas principales -->
    {{--<livewire:dashboard-cards />--}}

    <!-- Gráficas -->
    {{--<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <livewire:dashboard-charts />
    </div>

    <!-- Listados rápidos -->
    <div class="mt-6">
        <livewire:dashboard-tables />
    </div>--}}
    
</div>

{{-- 
<div class="divide-y-2 divide-gray-200">
    @hasanyrole('Administrador del sistema|vendedor|cliente')
        <div x-data="{ open: true }"
            class="bg-white flex flex-col items-center justify-center relative overflow-hidden w-full rounded-xl shadow-lg">
            <div @click="open = ! open" class="bg-gradient-to-r from-blue-500 to-green-500 p-6 w-full flex justify-between items-center cursor-pointer rounded-t-xl transition-all hover:opacity-90">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days pl-5 text-white"></i>
                    <p class="ml-4 text-lg text-white leading-7 font-semibold">
                        Citas próximas:
                    </p>
                </div>
                <i class="fas fa-chevron-down fa-lg text-white transition-transform duration-300"
                    :class="{ 'rotate-180': open }"></i>
            </div>
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-0" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-10"
                x-transition:leave-end="opacity-0 translate-y-0" class="w-full bg-white">

                <div class="bg-gray-200 bg-opacity-25 flex flex-col items-center justify-center px-4 py-8">
                    <div class="container block justify-center m-auto" wire:loading.remove>
                        <div class="text-center mb-8 mt-2">
                            <div class="grid lg:gap-x-12 lg:grid-cols-3">
                                <div class="mb-12 lg:mb-0">
                                    <div class="rounded-lg shadow-lg h-full block bg-white border border-gray-200">
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-blue-500 mb-4">10</h3>
                                            <h5 class="text-lg font-medium text-gray-700 mb-4">Pendientes</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-12 lg:mb-0">
                                    <div class="rounded-lg shadow-lg h-full block bg-white border border-gray-200">
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-green-500 mb-4">10</h3>
                                            <h5 class="text-lg font-medium text-gray-700 mb-4">Aceptadas</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="rounded-lg shadow-lg h-full block bg-white border border-gray-200">
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-red-500 mb-4">10</h3>
                                            <h5 class="text-lg font-medium text-gray-700 mb-4">Rechazadas</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endhasanyrole
</div>
--}}

{{--
<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900">
                <a href="https://laravel.com/docs">Documentation</a>
            </h2>
        </div>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900">
                <a href="https://laracasts.com">Laracasts</a>
            </h2>
        </div>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900">
                <a href="https://tailwindcss.com/">Tailwind</a>
            </h2>
        </div>
    </div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900">
                Authentication
            </h2>
        </div>
    </div>
</div> --}}
