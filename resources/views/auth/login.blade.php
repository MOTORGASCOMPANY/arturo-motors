<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row bg-[#0B0F19]">        
        <!-- Panel Izquierdo: Branding & Visual Banner -->
        <div class="relative lg:w-1/2 flex flex-col justify-between p-8 lg:p-12 overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#0B0F19] to-[#0284C7]/20 border-b lg:border-b-0 lg:border-r border-slate-800">            
            <!-- Glow FX -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header (Logo y Botón Volver) -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/icon.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                    </div>
                    <div>
                        <span class="font-extrabold text-xl tracking-tight text-white block leading-none">ARTURO <span class="text-sky-400">MOTORS</span></span>
                        <span class="text-[10px] text-slate-400 tracking-widest font-semibold uppercase">Gestión Taller</span>
                    </div>
                </a>
                <a href="/" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-2 bg-slate-800/50 hover:bg-slate-800 px-3 py-2 rounded-lg border border-slate-700/50 transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Volver a la Web</span>
                </a>
            </div>

            <!-- Middle Content Hero -->
            <div class="relative z-10 my-12 lg:my-0 max-w-md">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-semibold mb-6">
                    <i class="fa-solid fa-shield-halved"></i> Sistema de Control Interno
                </div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight mb-4">
                    Bienvenido al Portal de <span class="bg-gradient-to-r from-sky-400 to-blue-500 bg-clip-text text-transparent">Gestión Automotriz</span>
                </h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Accede para administrar conversiones GNV/GLP, certificaciones oficiales, historial de clientes y mantenimiento de taller en tiempo real.
                </p>
            </div>

            <!-- Footer Badge -->
            <div class="relative z-10 text-xs text-slate-500 flex items-center justify-between pt-6 border-t border-slate-800/60">
                <span>&copy; {{ date('Y') }} Arturo Motors</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sistema Operativo</span>
            </div>
        </div>

        <!-- Panel Derecho: Formulario de Login -->
        <div class="lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative">
            <div class="w-full max-w-md space-y-8">                
                <div class="text-center lg:text-left">
                    <h2 class="text-2xl font-bold text-white tracking-tight">Iniciar Sesión</h2>
                    <p class="text-sm text-slate-400 mt-1">Ingresa tus credenciales autorizadas para acceder</p>
                </div>
                <!-- Manejo de Errores y Estatus -->
                <x-validation-errors class="mb-4 bg-red-500/10 border border-red-500/20 p-4 rounded-xl text-red-400 text-sm" />

                @session('status')
                    <div class="mb-4 font-medium text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-xl">
                        {{ $value }}
                    </div>
                @endsession

                <!-- Formulario -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <!-- Email Input -->
                    <div>
                        <x-label for="email" value="{{ __('Correo Electrónico') }}" class="text-slate-300 font-medium mb-1 text-xs uppercase tracking-wider" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <x-input id="email" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500/20 text-sm transition-all" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username" 
                                placeholder="usuario@arturomotors.com" />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <x-label for="password" value="{{ __('Contraseña') }}" class="text-slate-300 font-medium mb-1 text-xs uppercase tracking-wider" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <x-input id="password" 
                                class="block w-full pl-10 pr-4 py-3 bg-slate-900/60 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500/20 text-sm transition-all" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password" 
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <!-- Opciones Adicionales (Recordar & Olvidé contraseña) -->
                    <div class="flex items-center justify-between text-sm pt-1">
                        <label for="remember_me" class="flex items-center cursor-pointer">
                            <x-checkbox id="remember_me" name="remember" class="rounded border-slate-700 bg-slate-900 text-sky-500 focus:ring-sky-500/20" />
                            <span class="ms-2 text-xs text-slate-400 hover:text-slate-300">{{ __('Recordarme') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-xs text-sky-400 hover:text-sky-300 transition-colors" href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Botón Submit -->
                    <div class="pt-2">
                        <x-button class="w-full justify-center py-3 px-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-sky-500/20 hover:shadow-sky-500/30 active:scale-[0.98] transition-all border-0">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> {{ __('Iniciar Sesión') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    {{-- 
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Correo') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Acuérdate de mí') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Iniciar Sesion') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
    --}}
</x-guest-layout>
