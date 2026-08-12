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

{{-- Iframe real de la página, scrolleado a esta sección, zoom out --}}
<div wire:ignore.self class="rounded-xl overflow-hidden border border-gray-200 bg-white relative" style="height: 500px;">
    <div style="width: 100%; height: 100%; overflow: hidden;">
        <iframe
            wire:ignore
            src="{{ $iframeUrl }}"
            class="border-0"
            style="width: 200%; height: 200%; transform: scale(0.5); transform-origin: top left; pointer-events: none;"
            loading="lazy"
            sandbox="allow-scripts allow-same-origin"
            title="Preview de {{ $section['title'] }}"
        ></iframe>
    </div>
</div>
