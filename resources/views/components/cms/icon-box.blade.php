<div {{ $attributes->merge(['class' => 'w-11 h-11 rounded-xl flex items-center justify-center border shadow-sm']) }}
     class="{{ $bgClass ?? 'bg-blue-50 border-blue-200 text-blue-600' }}">
    <i class="{{ $icon ?? 'fa-solid fa-cog' }}"></i>
</div>