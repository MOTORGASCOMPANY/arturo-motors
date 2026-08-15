<div {{ $attributes->merge(['class' => 'w-10 h-10 rounded-xl flex items-center justify-center border']) }}
     class="{{ $bgClass ?? 'bg-blue-50 border-blue-100 text-blue-600' }}">
    <i class="{{ $icon ?? 'fa-solid fa-cog' }}"></i>
</div>