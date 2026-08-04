<div class="space-y-6">
    <!-- Bienvenida -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-sky-500 rounded-xl shadow-lg text-white p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 opacity-20">
            <i class="fas fa-car-side text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">
                ¡Hola, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="mt-2 text-sky-100 font-medium text-lg">
                Bienvenido al Sistema de Gestión Automotriz Arturo Motors.
            </p>
            <div class="mt-4 flex items-center gap-4">
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-calendar-day mr-2"></i>{{ now()->format('d/m/Y') }}
                </span>
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-2"></i>{{ now()->format('H:i') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clientes -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Clientes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalClientes }}</p>
                    <p class="text-xs text-green-500 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>Activos en el sistema
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Vehículos -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Vehículos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalVehiculos }}</p>
                    <p class="text-xs text-green-500 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>Registrados
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-car text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Citas -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Citas</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCitas }}</p>
                    <p class="text-xs text-yellow-500 mt-1">
                        <i class="fas fa-clock mr-1"></i>{{ $citasPendientes }} pendientes
                    </p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Citas Hoy -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Citas Hoy</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $citasHoy }}</p>
                    <p class="text-xs text-purple-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>{{ now()->format('d/m/Y') }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-day text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Acciones Rápidas
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('CrearCita') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-all duration-300 group">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nueva Cita</span>
            </a>
            <a href="{{ route('ListaClientes') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-all duration-300 group">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nuevo Cliente</span>
            </a>
            <a href="{{ route('ListaVehiculos') }}" class="flex flex-col items-center p-4 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-all duration-300 group">
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-car text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nuevo Vehículo</span>
            </a>
            <a href="{{ route('ordenes.simple.crear') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-all duration-300 group">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-tools text-white"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nueva Orden</span>
            </a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Últimas Citas -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Últimas Citas
                </h3>
                <a href="{{ route('ListaCitas') }}" class="text-sm text-blue-500 hover:text-blue-700 font-medium">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            @if($ultimasCitas->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>No hay citas registradas</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($ultimasCitas as $cita)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $cita->cliente->nombre ?? 'N/A' }} {{ $cita->cliente->apellido ?? '' }}</p>
                                    <p class="text-xs text-gray-500">{{ $cita->fecha_cita }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $cita->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($cita->estado === 'aceptada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Últimos Clientes -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-users text-green-500 mr-2"></i>Últimos Clientes
                </h3>
                <a href="{{ route('ListaClientes') }}" class="text-sm text-green-500 hover:text-green-700 font-medium">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            @if($ultimosClientes->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-user-slash text-4xl mb-3"></i>
                    <p>No hay clientes registrados</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($ultimosClientes as $cliente)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                    <p class="text-xs text-gray-500">{{ $cliente->documento }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $cliente->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Resumen del Día -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold mb-2">
                    <i class="fas fa-chart-line mr-2"></i>Resumen del Día
                </h3>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $citasHoy }}</p>
                        <p class="text-sm text-green-100">Citas para hoy</p>
                    </div>
                    <div class="w-px h-12 bg-green-400"></div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $citasPendientes }}</p>
                        <p class="text-sm text-green-100">Pendientes</p>
                    </div>
                    <div class="w-px h-12 bg-green-400"></div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $totalClientes }}</p>
                        <p class="text-sm text-green-100">Total clientes</p>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-pie text-6xl opacity-30"></i>
            </div>
        </div>
    </div>
</div>