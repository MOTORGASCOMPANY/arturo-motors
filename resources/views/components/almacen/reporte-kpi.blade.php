@props(['value', 'label', 'icon', 'color' => 'blue', 'prefix' => ''])

@php
    $borderColorMap = [
        'blue'   => 'border-l-blue-500',
        'emerald'=> 'border-l-emerald-500',
        'red'    => 'border-l-red-500',
        'amber'  => 'border-l-amber-500',
        'indigo' => 'border-l-indigo-500',
        'slate'  => 'border-l-slate-300',
    ];
    $iconBgMap = [
        'blue'   => 'bg-blue-50',
        'emerald'=> 'bg-emerald-50',
        'red'    => 'bg-red-50',
        'amber'  => 'bg-amber-50',
        'indigo' => 'bg-indigo-50',
        'slate'  => 'bg-slate-50',
    ];
    $iconColorMap = [
        'blue'   => 'text-blue-600',
        'emerald'=> 'text-emerald-600',
        'red'    => 'text-red-600',
        'amber'  => 'text-amber-600',
        'indigo' => 'text-indigo-600',
        'slate'  => 'text-slate-400',
    ];
    $valueColorMap = [
        'blue'   => 'text-slate-800',
        'emerald'=> 'text-slate-800',
        'red'    => 'text-red-600',
        'amber'  => 'text-amber-600',
        'indigo' => 'text-indigo-600',
        'slate'  => 'text-slate-800',
    ];
@endphp

<div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 {{ $borderColorMap[$color] ?? $borderColorMap['blue'] }} p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-xl {{ $iconBgMap[$color] ?? $iconBgMap['blue'] }} flex items-center justify-center shrink-0">
        <i class="fas {{ $icon }} {{ $iconColorMap[$color] ?? $iconColorMap['blue'] }}"></i>
    </div>
    <div class="min-w-0">
        <p class="text-2xl font-extrabold {{ $valueColorMap[$color] ?? $valueColorMap['blue'] }} leading-none">{{ $prefix }}{{ $value }}</p>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $label }}</span>
    </div>
</div>
