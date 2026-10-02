    @if ($this->mostrarChecklist)
        <x-dialog-modal wire:model="mostrarChecklist" maxWidth="lg">
            <x-slot name="title">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-white/70"></i> Confirmar envío
                        </h3>
                        <p class="text-xs text-slate-300 mt-0.5 truncate">
                            <i class="fas fa-location-dot mr-1"></i>{{ $this->sedes->firstWhere('id', $this->sedeDestinoId)?->nombre ?? '' }}
                            <span class="text-white/40 mx-1">·</span>
                            {{ $this->seleccionCount }} items
                        </p>
                    </div>
                    <button wire:click="cerrarChecklist" type="button" class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition w-9 h-9 flex items-center justify-center shrink-0 -mr-1">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="max-h-[65vh] overflow-y-auto">
                    @php
                        $kitsIncompletos = collect($this->checklistData)
                            ->where('tipo', 'kit')
                            ->where('es_completo', false);
                    @endphp

                    @if ($kitsIncompletos->isNotEmpty())
                        <div class="flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2.5 mb-3 text-xs text-amber-800">
                            <i class="fas fa-triangle-exclamation mt-0.5 shrink-0"></i>
                            <span>
                                <strong>{{ $kitsIncompletos->count() }} {{ \Illuminate\Support\Str::plural('kit', $kitsIncompletos->count()) }}</strong>
                                viaja/n con piezas faltantes. Se despacha igual, pero queda marcado como incompleto.
                            </span>
                        </div>
                    @endif

                    <div class="space-y-3">

                        @forelse ($this->checklistData as $comp)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">

                                <div class="flex items-center gap-2 sm:gap-3 p-3 bg-white">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                        {{ $comp['tipo'] === 'kit' ? 'bg-emerald-100' : ($comp['tipo'] === 'pieza' ? 'bg-blue-100' : 'bg-amber-100') }}">
                                        <i class="fas text-sm
                                            {{ $comp['tipo'] === 'kit' ? 'fa-box text-emerald-600' : ($comp['tipo'] === 'pieza' ? 'fa-puzzle-piece text-blue-600' : 'fa-cubes text-amber-600') }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <span class="text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</span>
                                            @if ($comp['tipo'] === 'kit')
                                                @if ($comp['es_completo'])
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 shrink-0">Completo</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">Incompleto</span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400 truncate block mt-0.5">{{ $comp['detalle'] }}</span>
                                    </div>
                                </div>

                                @if ($comp['tipo'] === 'kit' && isset($comp['componentes']))
                                    <div class="px-3 pb-3 pt-2 bg-gray-50 border-t border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                            {{ $comp['totalPresente'] }}/{{ $comp['totalEsperado'] }} piezas
                                        </p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                            @foreach ($comp['componentes'] as $c)
                                                <div class="flex items-center gap-1.5 text-[11px] p-1.5 rounded
                                                    {{ $c['completo'] ? 'text-green-700 bg-green-50/50' : 'text-red-600 bg-red-50/50' }}">
                                                    <i class="fas {{ $c['completo'] ? 'fa-check text-green-500' : 'fa-times text-red-500' }} text-[9px] shrink-0"></i>
                                                    <span class="truncate">{{ $c['nombre'] }}</span>
                                                    <span class="text-gray-400 ml-auto shrink-0 font-medium">{{ $c['presente'] }}/{{ $c['esperada'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium text-gray-500">No hay items en el envío</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex w-full items-center gap-3">
                    <button wire:click="cerrarChecklist" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 active:scale-[0.98] transition font-medium text-sm">
                        Volver
                    </button>
                    <button wire:click="confirmarEnvio" wire:loading.attr="disabled" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl transition font-semibold text-sm flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="confirmarEnvio"><i class="fas fa-check mr-1"></i> Confirmar envío</span>
                        <span wire:loading wire:target="confirmarEnvio"><i class="fas fa-circle-notch fa-spin mr-1"></i> Enviando...</span>
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>
    @endif
