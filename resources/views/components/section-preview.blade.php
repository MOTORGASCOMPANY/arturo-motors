@props(['section', 'refreshKey' => '', 'highlight' => false])

@php
    $anchorMap = [
        'hero' => 'inicio',
        'about' => 'nosotros',
        'services' => 'servicios',
        'why' => 'proceso',
        'process' => 'proceso',
        'contact' => 'contacto',
    ];
    $anchor = $anchorMap[$section['key']] ?? 'contacto';
    $baseUrl = url('/');
    $params = [];
    if ($refreshKey) {
        $params[] = "v={$refreshKey}";
    }
    if ($highlight) {
        $params[] = 'highlight=1';
    }
    $queryString = $params ? '?' . implode('&', $params) : '';
    $iframeUrl = "{$baseUrl}{$queryString}#{$anchor}";
@endphp

{{-- Todo el bloque va wire:ignore para que Livewire nunca vuelva a tocarlo --}}
<div wire:ignore class="rounded-xl overflow-hidden border border-gray-200 bg-white relative" style="height: 500px;">

    {{-- Skeleton: se oculta por JS cuando el iframe termina de cargar --}}
    <div class="preview-skeleton absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
        <div class="flex flex-col items-center gap-2">
            <div class="w-6 h-6 border-2 border-blue-200 border-t-blue-600 rounded-full" style="animation: spin 0.7s linear infinite"></div>
            <span class="text-[11px] text-gray-400">Cargando vista previa...</span>
        </div>
    </div>

    <div style="width: 100%; height: 100%; overflow: hidden;">
        <iframe
            src="{{ $iframeUrl }}"
            class="border-0 preview-iframe"
            style="width: 200%; height: 200%; transform: scale(0.5); transform-origin: top left; pointer-events: none;"
            loading="lazy"
            tabindex="-1"
            sandbox="allow-scripts allow-same-origin"
            title="Preview de {{ $section['title'] }}"
        ></iframe>
    </div>
</div>