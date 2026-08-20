<div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="cmsLayout()" x-init="init()">
    {{-- Sidebar --}}
    <aside x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-full" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 transform lg:translate-x-0" :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }" @keydown.escape.window="sidebarOpen = false" aria-label="Navegación principal">
        <!-- Sidebar Header -->
        <div class="flex flex-col h-full">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 text-gray-900 dark:text-white hover:opacity-80 transition-opacity" aria-label="Arturo Motors - Ir al inicio">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                        <i class="fa-solid fa-car text-white"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg">Arturo Motors</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Panel CMS</span>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto" aria-label="Secciones CMS">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">CONTENIDO</div>
                <a href="{{ route('cms.contenido') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.contenido') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-solid fa-layer-group w-5 text-center"></i>
                    <span>Contenido Principal</span>
                </a>
                <a href="{{ route('cms.servicios') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.servicios') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-solid fa-wrench w-5 text-center"></i>
                    <span>Servicios</span>
                </a>
                <a href="{{ route('cms.pasos') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.pasos') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-solid fa-route w-5 text-center"></i>
                    <span>Proceso / Pasos</span>
                </a>
                <a href="{{ route('cms.porque') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.porque') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                    <span>Por Qué Elegirnos</span>
                </a>

                <div class="pt-4 px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">CONTACTO Y REDES</div>
                <a href="{{ route('cms.contacto') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.contacto') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-solid fa-location-dot w-5 text-center"></i>
                    <span>Información de Contacto</span>
                </a>
                <a href="{{ route('cms.redes') }}" class="cms-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('cms.redes') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" @click="sidebarOpen = false">
                    <i class="fa-brands fa-instagram w-5 text-center"></i>
                    <span>Redes Sociales</span>
                </a>


            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('landing') }}" target="_blank" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition-all" @click="sidebarOpen = false">
                    <i class="fa-solid fa-eye w-5 text-center"></i>
                    <span>Ver Landing Page</span>
                </a>
            </div>
        </div>
    </aside>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

    {{-- Main Content --}}
    <div class="lg:pl-64 min-h-screen bg-white">
        <!-- Top Bar -->
   

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            {{-- Page Header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    {!! $headerIcon ?? '<i class="fa-solid fa-cog text-blue-600"></i>' !!}
                    {{ $title ?? 'Gestión CMS' }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $description ?? 'Administra el contenido del landing page' }}</p>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => show = false, 4000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 shadow-sm animate-slide-down" role="alert">
                    <i class="fa-solid fa-circle-check text-lg flex-shrink-0"></i>
                    <span class="flex-1 font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 dark:text-green-400 hover:opacity-70 transition-opacity" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => show = false, 4000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 shadow-sm animate-slide-down" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-lg flex-shrink-0"></i>
                    <span class="flex-1 font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-500 dark:text-red-400 hover:opacity-70 transition-opacity" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            {{-- Component Slot --}}
            {{ $slot }}
        </main>
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function cmsLayout() {
            return {
                sidebarOpen: false,
                darkMode: false,
                userMenuOpen: false,
                init() {
                    // Dark mode
                    if (localStorage.getItem('cms-dark-mode') === 'true' ||
                        (!localStorage.getItem('cms-dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.enableDarkMode();
                    }
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        if (!localStorage.getItem('cms-dark-mode')) {
                            e.matches ? this.enableDarkMode() : this.disableDarkMode();
                        }
                    });

                    // Close user menu on escape
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            this.userMenuOpen = false;
                            this.sidebarOpen = false;
                        }
                    });
                },
                toggleDarkMode() {
                    this.darkMode ? this.disableDarkMode() : this.enableDarkMode();
                },
                enableDarkMode() {
                    this.darkMode = true;
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('cms-dark-mode', 'true');
                },
                disableDarkMode() {
                    this.darkMode = false;
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('cms-dark-mode', 'false');
                }
            }
        }
    </script>

    {{-- Custom Styles --}}
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }

        .cms-nav-item { transition: all 0.2s ease; }
        .cms-nav-item:hover { background-color: #f3f4f6; transform: translateX(4px); }
        .dark .cms-nav-item:hover { background-color: #1f2937; }

        .cms-nav-item:focus-visible,
        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        textarea:focus-visible,
        select:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }

        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</div>