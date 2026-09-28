@php
    // El paso 3 (componentes) reutiliza el flag $modalKitAbierto, pero se dibuja
    // como una sección de la página, no como un modal.
    $enComponentes = (bool) $this->modalKitAbierto;
    $pasosFlujo    = $this->seccion === 'productos' ? ['Datos', 'Productos'] : ['Datos', 'Kits', 'Componentes'];
    $pasoActual    = $this->seccion === 'elegir' && ! $enComponentes ? 1 : ($enComponentes ? 3 : 2);
@endphp

<ol class="flex items-center mb-8 text-xs font-semibold">
    @foreach ($pasosFlujo as $i => $nombrePaso)
        @php
            $n      = $i + 1;
            $hecho  = $n < $pasoActual;
            $activo = $n === $pasoActual;
        @endphp
        <li wire:key="paso-{{ $n }}" class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
            <span class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full flex items-center justify-center {{ $hecho ? 'bg-green-500 text-white' : ($activo ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                    @if ($hecho)
                        <i class="fas fa-check text-[10px]"></i>
                    @else
                        {{ $n }}
                    @endif
                </span>
                <span class="{{ $activo ? 'text-indigo-700' : 'text-gray-500' }}">{{ $nombrePaso }}</span>
            </span>
            @unless ($loop->last)
                <span class="flex-1 h-px mx-3 {{ $hecho ? 'bg-green-400' : 'bg-gray-200' }}"></span>
            @endunless
        </li>
    @endforeach
</ol>
