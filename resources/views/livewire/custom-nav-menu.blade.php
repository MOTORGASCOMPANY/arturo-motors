<!-- component -->
<!--
Change class "fixed" to "sticky" in "navbar" (l. 33) so the navbar doesn't hide any of your page content!
-->
<div>
    <style>
        ul.breadcrumb li+li::before {
            content: "";
            padding-left: 8px;
            padding-right: 4px;
            color: inherit;
        }

        ul.breadcrumb li span {
            opacity: 60%;
        }

        #sidebar {
            -webkit-transition: all 300ms cubic-bezier(0, 0.77, 0.58, 1);
            transition: all 300ms cubic-bezier(0, 0.77, 0.58, 1);
        }

        #sidebar.show {
            transform: translateX(0);
        }

        #sidebar ul li a.active {
            background: #1f2937;
            background-color: #1f2937;
        }
    </style>

    <!-- Navbar start -->
    <nav id="navbar" class="fixed top-0 z-40 flex w-full flex-row justify-between bg-gradient-to-r from-gray-400 to-blue-600 px-4 shadow-lg border-b border-gray-300">
        <button id="btnSidebarToggler" type="button" class="py-4 text-2xl text-white hover:text-gray-200">
            <svg id="navClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg id="navOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="hidden h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="py-2 h-1/2">
            <img src="{{ asset('images/arturo3.png') }}" width="150" />
        </a>

        <div class="hidden  md:flex  md:items-center">
            <x-dropdown width="48">
                <x-slot name="trigger">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <button
                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        </button>
                    @else
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white  hover:text-black focus:outline-none transition">
                                <i class="fa-solid fa-user-gear fa-lg"></i>
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    @endif
                </x-slot>

                <x-slot name="content">
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Administrar Cuenta') }}
                    </div>

                    <x-dropdown-link href="{{ route('profile.show') }}">
                        {{ __('Perfil') }}
                    </x-dropdown-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-dropdown-link href="{{ route('api-tokens.index') }}">
                            {{ __('API Tokens') }}
                        </x-dropdown-link>
                    @endif

                    <div class="border-t border-gray-100"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('Salir') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

    </nav>
    <!-- Navbar end -->

    <!-- Sidebar start-->
    <div id="containerSidebar" class="z-40">
        <div class="navbar-menu relative z-40">
            <nav id="sidebar" class="fixed left-0 bottom-0 flex w-3/4 -translate-x-full flex-col bg-gradient-to-br from-slate-600 via-blue-700 to-slate-600 pt-2 sm:max-w-xs lg:w-80">
                <!-- one category / navigation group -->
                <div class="px-4 overflow-y-auto">
                    <h3 class="mb-2 text-xs font-medium uppercase text-gray-300">
                        Menu principal
                    </h3>
                    <ul class="text-sm font-medium">
                        <li>
                            <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600"
                                href="{{ route('inicio') }}">
                                <i class="fas fa-home -mt-1"></i>
                                <span class="select-none">Inicio</span>
                            </a>
                        </li>

                        {{--             OPCIONES PARA CITAS                    --}}
                        {{-- 
                        @can('opciones.citas')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <span class="select-none">Citas</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaCitas') }}"
                                                :active="request()->routeIs('ListaCitas')">
                                                Lista citas
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan
                        --}}

                        {{--             OPCIONES PARA SERVICIOS                    --}}
                        @can('opciones.servicios')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                        <span class="select-none">Servicios</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaCitas') }}"
                                                :active="request()->routeIs('ListaCitas')">
                                                Lista citas
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        {{--             OPCIONES PARA CONVERSIONES                    --}}
                        @can('opciones.conversiones')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-car"></i>
                                        <span class="select-none">Conversiones</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaConversiones') }}"
                                                :active="request()->routeIs('ListaConversiones')">
                                                Conversiones
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan   
                        
                        {{--             OPCIONES PARA EXPEDIENTES                    --}}
                        @can('opciones.expedientes')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-folder-open"></i>
                                        <span class="select-none">Expedientes</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaExpedientes') }}"
                                                :active="request()->routeIs('ListaExpedientes')">
                                                Expedientes
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        {{--             OPCIONES PARA EGRESOS --}}
                        @can('opciones.egresos')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-cash-register"></i>
                                        <span class="select-none">Egresos</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <x-responsive-nav-link class="text-sm">
                                            {{ __('Egresos') }}
                                        </x-responsive-nav-link>

                                    </ul>

                                </div>
                            </li>
                        @endcan

                        {{--             OPCIONES PARA CLIENTES                    
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-users"></i>
                                        <span class="select-none">Clientes</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaClientes') }}"
                                                :active="request()->routeIs('ListaClientes')">
                                                Clientes
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>--}}

                        {{--             OPCIONES PARA ALMACEN                    --}}
                        @can('opciones.almacen')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-store"></i>
                                        <span class="select-none">Almacen</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('Repuestos') }}"
                                                :active="request()->routeIs('Repuestos')">
                                                Almacen
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan
                        
                        {{--             OPCIONES PARA RR.HH --}}
                        @can('opciones.rrhh')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-flag -mt-1"></i>
                                        <span class="select-none">RRHH</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">
                                        
                                        @can('rrhh.contratos')
                                            <x-responsive-nav-link class="text-sm" href="{{ route('rrhh.contratos') }}"
                                                :active="request()->routeIs('rrhh.contratos')">
                                                {{ __('Empleados') }}
                                            </x-responsive-nav-link>
                                        @endcan
                                        @can('rrhh.planillas')
                                            <x-responsive-nav-link class="text-sm" href="{{ route('rrhh.planillas') }}"
                                                :active="request()->routeIs('rrhh.planillas')">
                                                {{ __('Planillas') }}
                                            </x-responsive-nav-link>
                                        @endcan
                                        <x-responsive-nav-link class="text-sm" href="{{ route('rrhh.mis-planillas') }}"
                                            :active="request()->routeIs('rrhh.mis-planillas')">
                                            {{ __('Boletas') }}
                                        </x-responsive-nav-link>

                                    </ul>

                                </div>
                            </li>
                        @endcan

                        {{--             OPCIONES PARA REPORTES                    --}}
                        @can('opciones.reportes')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-chart-column"></i>
                                        <span class="select-none">Reportes</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('Rpta.Citas') }}"
                                                :active="request()->routeIs('Rpta.Citas')">
                                                Reporte de Citas
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        {{--             OPCIONES PARA MANTENIMIENTO TABLAS                    --}}
                        @can('opciones.vehiculos')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                        <i class="fa-solid fa-database"></i>
                                        <span class="select-none">Mantenimiento Tablas</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaVehiculos') }}"
                                                :active="request()->routeIs('ListaVehiculos')">
                                                Lista Vehiculos
                                            </x-responsive-nav-link>
                                        </li>
                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('ListaClientes') }}"
                                                :active="request()->routeIs('ListaClientes')">
                                                Lista Clientes
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        {{--            OPCIONES PARA USUARIOS Y ROLES                  --}}
                        @can('opciones.usuarios')
                            <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                x-data="{ Open: false }">
                                <div class="inline-flex  items-center justify-between w-full  transition-colors duration-150 text-gray-500  cursor-pointer"
                                    x-on:click="Open = !Open">
                                    <span class="inline-flex items-center space-x-6  text-sm  text-white ">
                                        <i class="fa-solid fa-user-shield"></i>
                                        <span class="select-none">Usuarios y roles</span>
                                    </span>
                                    <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                    <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                </div>
                                <div x-show.transition="Open" style="display:none;">
                                    <ul x-transition:enter="transition-all ease-in-out duration-300"
                                        x-transition:enter-start="opacity-25 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-xl"
                                        x-transition:leave="transition-all ease-in-out duration-300"
                                        x-transition:leave-start="opacity-100 max-h-xl"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                        aria-label="submenu">

                                        @can('usuarios')
                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('usuarios') }}"
                                                :active="request()->routeIs('usuarios')">
                                                Usuarios
                                            </x-responsive-nav-link>
                                        </li>
                                        @endcan
                                        @can('usuarios.roles')
                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm" href="{{ route('usuarios.roles') }}"
                                                :active="request()->routeIs('usuarios.roles')">
                                                Roles
                                            </x-responsive-nav-link>
                                        </li>
                                        @endcan
                                        @can('usuarios.permisos')
                                        <li class="transition-colors duration-150">
                                            <x-responsive-nav-link class="text-sm"
                                                href="{{ route('usuarios.permisos') }}" :active="request()->routeIs('usuarios.permisos')">
                                                Permisos
                                            </x-responsive-nav-link>
                                        </li>
                                        @endcan
                                    </ul>

                                </div>
                            </li>
                        @endcan

                    </ul>
                </div>



                <!-- navigation group end-->
                <!-- opciones de cuenta de usuario -->
                <div class="md:hidden block bg-gradient-to-br from-gray-400 to-blue-600 bottom-0 left-0 px-4 w-full z-10 mt-2">
                    <h3 class="my-2 text-xs font-medium uppercase text-gray-500">
                        Opciones de la cuenta
                    </h3>
                    <ul class="mb-2 text-sm font-medium ">
                        <li>
                            <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600 "
                                href="{{ route('profile.show') }}">
                                <i class="fa-solid fa-user-gear -mt-1"></i>
                                <span class="select-none">Configurar Perfil</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600 "
                                    href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    <i class="fa-solid fa-arrow-right-from-bracket -mt-1"></i>
                                    <span class="select-none">Salir</span>
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
                <!-- fin -->
            </nav>
        </div>

    </div>
    <!-- Sidebar end -->

    <main>
        <!-- your content goes here -->
    </main>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("navbar");
            const sidebar = document.getElementById("sidebar");
            const btnSidebarToggler = document.getElementById("btnSidebarToggler");
            const navClosed = document.getElementById("navClosed");
            const navOpen = document.getElementById("navOpen");

            btnSidebarToggler.addEventListener("click", (e) => {
                e.preventDefault();
                sidebar.classList.toggle("show");
                navClosed.classList.toggle("hidden");
                navOpen.classList.toggle("hidden");
            });

            sidebar.style.top = parseInt(navbar.clientHeight) - 1 + "px";
        });
    </script>
</div>
