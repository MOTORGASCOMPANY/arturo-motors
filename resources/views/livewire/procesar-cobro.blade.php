<div>
    @if (!$completado)
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                <i class="fas fa-wallet text-gray-400"></i> Detalle del Pago
            </h3>

            <div class="p-4 bg-emerald-50 border border-emerald-200 text-center rounded-xl">
                <span class="text-xs uppercase tracking-wider text-emerald-700 font-bold block mb-1">Monto Total a Cobrar</span>
                <div class="text-3xl font-black text-emerald-700">S/ {{ number_format($monto, 2) }}</div>
            </div>

            <div>
                <x-label for="metodoPago" value="Método de pago" class="font-semibold text-gray-700 mb-1" />
                <select wire:model="metodoPago" id="metodoPago" 
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    <option value="efectivo">💵 Efectivo</option>
                    <option value="tarjeta">💳 Tarjeta (Débito/Crédito)</option>
                    <option value="transferencia">📲 Transferencia / Yape / Plin</option>
                    <option value="otro">Otros</option>
                </select>
                <x-input-error for="metodoPago" class="mt-1" />
            </div>

            <x-input-error for="caja" class="mt-1" />

            <button wire:click="procesar" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Confirmar cobro
            </button>
        </div>
    @else
        <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm text-center space-y-4 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-check text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">¡Cobro completado!</h3>
            <p class="text-sm text-gray-600">
                Comprobante generado: <span class="font-bold text-gray-800">{{ $folioGenerado }}</span>
            </p>

            @if($orden)
                <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('comprobantes.pdf', $orden->id) }}" target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-file-pdf"></i> Ver comprobante PDF
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>