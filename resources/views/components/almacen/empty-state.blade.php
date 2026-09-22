@props(['icono' => 'fa-boxes-stacked', 'mensaje' => 'No hay datos para mostrar'])

<div class="bg-white rounded-xl border border-gray-200 px-6 py-14 text-center">
    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class="fas {{ $icono }} text-gray-400 text-xl"></i>
    </div>
    <p class="text-gray-500 text-sm font-medium">{{ $mensaje }}</p>
</div>
