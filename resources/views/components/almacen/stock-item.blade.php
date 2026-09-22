@props(['producto', 'sede', 'cantidad', 'href' => null])

<li class="flex items-center gap-3 px-4 py-2.5">
    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
        <i class="fas fa-cubes text-indigo-500 text-sm"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-gray-800 truncate">{{ $producto ?? 'Producto' }}</p>
        <p class="text-xs text-gray-500">{{ $sede ?? '—' }}</p>
    </div>
    <div class="text-right shrink-0">
        <p class="text-xl font-black text-indigo-600 leading-none tabular-nums">{{ $cantidad }}</p>
        <p class="text-xs text-gray-400 mt-0.5">unidades</p>
    </div>
</li>
