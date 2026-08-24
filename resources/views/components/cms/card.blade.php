<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden hover:shadow-lg hover:border-blue-200/60 transition-all duration-300 relative before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-gradient-to-r before:from-blue-500 before:to-blue-600 before:rounded-t-2xl']) }}>
    {{ $slot }}
</div>