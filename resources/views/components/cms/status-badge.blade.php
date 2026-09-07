<span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full
    {{ $active
        ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
        : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
    {{ $active ? 'Activo' : 'Inactivo' }}
</span>
