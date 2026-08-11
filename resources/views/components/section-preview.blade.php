@props(['section'])

{{-- Iframe real de la página, scrolleado a esta sección, zoom out --}}
<div class="rounded-xl overflow-hidden border border-gray-200 bg-white relative" style="height: 500px;">
    <div style="width: 100%; height: 100%; overflow: hidden;">
        <iframe
            src="{{ url('/') }}#{{ $section['key'] === 'hero' ? 'inicio' : ($section['key'] === 'about' ? 'nosotros' : ($section['key'] === 'services' ? 'servicios' : ($section['key'] === 'why' ? 'proceso' : ($section['key'] === 'process' ? 'proceso' : 'contacto')))) }}"
            class="border-0"
            style="width: 200%; height: 200%; transform: scale(0.5); transform-origin: top left; pointer-events: none;"
            loading="lazy"
            sandbox="allow-scripts allow-same-origin"
            title="Preview de {{ $section['title'] }}"
        ></iframe>
    </div>
</div>
