<span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full
    {{ $active
        ? 'bg-green-100 text-green-700 border border-green-200 shadow-sm shadow-green-100/50'
        : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
    {{ $active ? 'Activo' : 'Inactivo' }}
</span>