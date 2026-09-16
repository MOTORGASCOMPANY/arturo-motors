<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300 overflow-hidden">

        <div class="p-6 border-b border-gray-300 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Evaluación técnica previa</h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $orden->cliente?->nombre }} {{ $orden->cliente?->apellido }} —
                    <span class="font-semibold text-gray-800">{{ $orden->vehiculo?->placa }}</span> 
                    ({{ $orden->vehiculo?->marca }} {{ $orden->vehiculo?->modelo }})
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" wire:click="marcarTodo" class="text-xs font-medium text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Marcar todo
                </button>
                <button type="button" wire:click="desmarcarTodo" class="text-xs font-medium text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Desmarcar todo
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <x-input-error for="general" />

            <div class="flex flex-col lg:flex-row gap-6">

                <div class="flex-1 space-y-4">
                    @foreach ($gruposChecklist as $grupo => $items)
                        <div>
                            <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">{{ $grupo }}</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach ($items as $clave => $label)
                                    <label class="flex items-center gap-2 text-sm border rounded-lg px-3 py-2 cursor-pointer transition-colors
                                                  {{ ($checklist[$clave] ?? false) ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-medium shadow-xs' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                                        <input type="checkbox" wire:model.live="checklist.{{ $clave }}"
                                               class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <span class="select-none">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex-1">
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">
                        <i class="fas fa-pen-fancy mr-1"></i> Ficha de daño exterior
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">Marque con el lápiz las imperfecciones, rayones o daños que presenta el vehículo al llegar.</p>

                    <div x-data="fichaDano()" x-init="init()" class="bg-white border border-gray-300 rounded-xl p-3">

                        <template x-if="fichaGuardada && fichaUrl">
                            <div>
                                <div class="rounded-lg overflow-hidden border border-emerald-200">
                                    <img :src="fichaUrl" class="w-full h-auto block">
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                        <i class="fas fa-check-circle"></i> Guardado con éxito
                                    </span>
                                    <button type="button" @click="reiniciarFicha()"
                                            class="text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">
                                        <i class="fas fa-sync-alt mr-1"></i> Modificar
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="!fichaGuardada && !drawStarted">
                            <div>
                                @if(!empty($fichaDanoUrl))
                                    <div class="rounded-lg overflow-hidden border border-gray-200">
                                        <img src="{{ $fichaDanoUrl }}" class="w-full h-auto block">
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                            <i class="fas fa-image text-gray-400"></i> Ficha previa cargada
                                        </span>
                                        <button type="button" @click="reiniciarFicha()"
                                                class="text-xs font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-3 py-1.5 rounded-lg transition">
                                            <i class="fas fa-sync-alt mr-1"></i> Modificar
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                        <i class="fas fa-pen-fancy text-3xl text-gray-300 mb-3"></i>
                                        <p class="text-sm text-gray-500 mb-4">Dibuja los daños del vehículo antes de llegar</p>
                                        <button type="button" @click="iniciarDibujo()"
                                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition flex items-center gap-2 cursor-pointer shadow-md">
                                            <i class="fas fa-pen"></i> Iniciar ficha
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </template>

                        <template x-if="!fichaGuardada && drawStarted">
                            <div>
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <button type="button" @click="setTool('pencil')"
                                            :class="tool === 'pencil' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                                        <i class="fas fa-pen"></i> Lápiz
                                    </button>
                                    <button type="button" @click="setTool('eraser')"
                                            :class="tool === 'eraser' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                                        <i class="fas fa-eraser"></i> Borrador
                                    </button>
                                    <div class="w-px h-6 bg-gray-300"></div>
                                    <button type="button" @click="setColor('#1a1a1a')" :class="color === '#1a1a1a' ? 'ring-2 ring-offset-1 ring-gray-900' : ''" class="w-6 h-6 rounded-full bg-gray-900 border border-gray-300 transition"></button>
                                    <button type="button" @click="setColor('#dc2626')" :class="color === '#dc2626' ? 'ring-2 ring-offset-1 ring-red-600' : ''" class="w-6 h-6 rounded-full bg-red-600 border border-gray-300 transition"></button>
                                    <button type="button" @click="setColor('#2563eb')" :class="color === '#2563eb' ? 'ring-2 ring-offset-1 ring-blue-600' : ''" class="w-6 h-6 rounded-full bg-blue-600 border border-gray-300 transition"></button>
                                    <button type="button" @click="setColor('#16a34a')" :class="color === '#16a34a' ? 'ring-2 ring-offset-1 ring-green-600' : ''" class="w-6 h-6 rounded-full bg-green-600 border border-gray-300 transition"></button>
                                    <div class="w-px h-6 bg-gray-300"></div>
                                    <label class="text-[11px] text-gray-500 font-medium">Grosor</label>
                                    <input type="range" min="1" max="8" x-model="strokeWidth" class="w-20 h-1 accent-gray-900">
                                    <div class="w-px h-6 bg-gray-300"></div>
                                    <button type="button" @click="undo()" class="px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition" title="Deshacer">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button type="button" @click="clearCanvas()" class="px-2.5 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition" title="Limpiar todo">
                                        <i class="fas fa-trash-alt"></i> Limpiar
                                    </button>
                                </div>

                                <div class="relative bg-gray-50 rounded-lg overflow-hidden border border-gray-200" style="touch-action: none;">
                                    <img x-ref="carImg" src="{{ asset('images/carro.png') }}" class="w-full h-auto block pointer-events-none select-none" draggable="false">
                                    <canvas x-ref="canvas"
                                            class="absolute inset-0 w-full h-full cursor-crosshair"
                                            @pointerdown="startDraw($event)"
                                            @pointermove="draw($event)"
                                            @pointerup="endDraw($event)"
                                            @pointerleave="endDraw($event)">
                                    </canvas>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[11px] text-gray-400" x-show="hasDrawings" x-transition>
                                        <i class="fas fa-check-circle text-green-500 mr-1"></i> Trazos realizados
                                    </span>
                                    <span class="text-[11px] text-gray-400" x-show="!hasDrawings">
                                        Sin marcas aún
                                    </span>
                                    <div class="flex gap-2">
                                        <button type="button" @click="cancelarDibujo()"
                                                class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                            Cancelar
                                        </button>
                                        <button type="button" @click="guardarFicha()" :disabled="saving"
                                                class="px-4 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                            <template x-if="!saving">
                                                <span><i class="fas fa-save mr-1"></i> Guardar ficha</span>
                                            </template>
                                            <template x-if="saving">
                                                <span><i class="fas fa-spinner fa-spin mr-1"></i> Guardando...</span>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <input type="hidden" wire:model.live="fichaDanoUrl">
                    </div>
                </div>

            </div>

            <div class="pt-2">
                <x-label for="observaciones" value="Observaciones (obligatorio si se rechaza)" class="font-medium text-gray-700" />
                <textarea wire:model="observaciones" rows="3"
                          class="w-full bg-white rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 mt-1 shadow-xs"
                          placeholder="Ej: falta llanta de repuesto y el vehículo tiene fuga de aceite"></textarea>
                <x-input-error for="observaciones" class="mt-1" />
            </div>
        </div>

        <div class="p-6 border-t border-gray-300 flex justify-between gap-3">
            <button @click="
                        const url = $wire.get('fichaDanoUrl');
                        if (!url) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sin ficha de daño',
                                text: 'No dibujaste ningún daño. ¿Estás seguro de continuar?',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Sí, guardar sin ficha',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.call('guardarEvaluacion', false, true);
                                }
                            });
                        } else {
                            $wire.call('guardarEvaluacion', false);
                        }
                    "
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion">No apto</span>
                <span wire:loading wire:target="guardarEvaluacion">Guardando...</span>
            </button>
            
            <button @click="
                        const url = $wire.get('fichaDanoUrl');
                        if (!url) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sin ficha de daño',
                                text: 'No dibujaste ningún daño. ¿Estás seguro de continuar?',
                                showCancelButton: true,
                                confirmButtonColor: '#059669',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Sí, continuar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.call('guardarEvaluacion', true, true);
                                }
                            });
                        } else {
                            $wire.call('guardarEvaluacion', true);
                        }
                    "
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion">Apto para conversión</span>
                <span wire:loading wire:target="guardarEvaluacion">Guardando...</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fichaDano', () => ({
        canvas: null,
        ctx: null,
        tool: 'pencil',
        color: '#dc2626',
        strokeWidth: 3,
        isDrawing: false,
        hasDrawings: false,
        drawStarted: false,
        fichaGuardada: false,
        fichaUrl: @js($fichaDanoUrl),
        saving: false,
        history: [],
        currentPath: [],

        init() {
            if (this.fichaUrl) {
                this.fichaGuardada = true;
            }
        },

        iniciarDibujo() {
            this.drawStarted = true;
            this.$nextTick(() => {
                this.initCanvas();
            });
        },

        initCanvas() {
            const c = this.$refs.canvas;
            if (!c) return false;
            if (this.canvas === c && this.ctx) return true;
            this.canvas = c;
            this.ctx = c.getContext('2d');
            this.resizeCanvas();
            window.removeEventListener('resize', this._resizeHandler);
            this._resizeHandler = () => this.resizeCanvas();
            window.addEventListener('resize', this._resizeHandler);
            return true;
        },

        cancelarDibujo() {
            this.drawStarted = false;
            this.history = [];
            this.hasDrawings = false;
            this.canvas = null;
            this.ctx = null;
        },

        reiniciarFicha() {
            this.fichaGuardada = false;
            this.fichaUrl = '';
            this.drawStarted = false;
            this.history = [];
            this.hasDrawings = false;
            this.canvas = null;
            this.ctx = null;
            this.$wire.call('limpiarFicha');
        },

        resizeCanvas() {
            if (!this.canvas || !this.ctx) return;
            const rect = this.canvas.parentElement.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            this.canvas.width = rect.width * dpr;
            this.canvas.height = rect.height * dpr;
            this.ctx.scale(dpr, dpr);
            this.canvas.style.width = rect.width + 'px';
            this.canvas.style.height = rect.height + 'px';
            this.redrawAll();
        },

        getCoords(e) {
            const rect = this.canvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            return {
                x: (e.clientX - rect.left) * (this.canvas.width / (rect.width * dpr)),
                y: (e.clientY - rect.top) * (this.canvas.height / (rect.height * dpr))
            };
        },

        startDraw(e) {
            if (!this.initCanvas()) return;
            this.isDrawing = true;
            const { x, y } = this.getCoords(e);
            this.currentPath = [{ x, y, color: this.tool === 'eraser' ? 'eraser' : this.color, width: this.tool === 'eraser' ? this.strokeWidth * 4 : this.strokeWidth }];
            this.ctx.beginPath();
            this.ctx.moveTo(x, y);
        },

        draw(e) {
            if (!this.isDrawing || !this.ctx) return;
            const { x, y } = this.getCoords(e);
            this.currentPath.push({ x, y });

            if (this.tool === 'eraser') {
                this.ctx.globalCompositeOperation = 'destination-out';
                this.ctx.lineWidth = this.strokeWidth * 4;
            } else {
                this.ctx.globalCompositeOperation = 'source-over';
                this.ctx.strokeStyle = this.color;
                this.ctx.lineWidth = this.strokeWidth;
            }
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.lineTo(x, y);
            this.ctx.stroke();
            this.ctx.beginPath();
            this.ctx.moveTo(x, y);
        },

        endDraw(e) {
            if (!this.isDrawing || !this.ctx) return;
            this.isDrawing = false;
            if (this.currentPath.length > 1) {
                this.history.push([...this.currentPath]);
                this.hasDrawings = true;
            }
            this.currentPath = [];
            this.ctx.globalCompositeOperation = 'source-over';
        },

        redrawAll() {
            if (!this.ctx) return;
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.history.forEach(path => {
                if (path.length < 2) return;
                this.ctx.beginPath();
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                for (let i = 0; i < path.length; i++) {
                    const pt = path[i];
                    if (pt.color === 'eraser') {
                        this.ctx.globalCompositeOperation = 'destination-out';
                        this.ctx.lineWidth = pt.width * 4;
                    } else {
                        this.ctx.globalCompositeOperation = 'source-over';
                        this.ctx.strokeStyle = pt.color;
                        this.ctx.lineWidth = pt.width;
                    }
                    if (i === 0) {
                        this.ctx.moveTo(pt.x, pt.y);
                    } else {
                        this.ctx.lineTo(pt.x, pt.y);
                        this.ctx.stroke();
                        this.ctx.beginPath();
                        this.ctx.moveTo(pt.x, pt.y);
                    }
                }
            });
            this.ctx.globalCompositeOperation = 'source-over';
        },

        undo() {
            this.history.pop();
            this.redrawAll();
            this.hasDrawings = this.history.length > 0;
        },

        clearCanvas() {
            this.history = [];
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.hasDrawings = false;
        },

        setTool(t) { this.tool = t; },
        setColor(c) { this.color = c; this.tool = 'pencil'; },

        async guardarFicha() {
            this.saving = true;
            try {
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = this.canvas.width;
                tempCanvas.height = this.canvas.height;
                const tempCtx = tempCanvas.getContext('2d');

                const carImg = this.$refs.carImg;
                tempCtx.drawImage(carImg, 0, 0, tempCanvas.width, tempCanvas.height);
                tempCtx.drawImage(this.canvas, 0, 0);

                const dataUrl = tempCanvas.toDataURL('image/webp', 0.8);

                // Call Livewire method instead of controller
                const success = await this.$wire.call('guardarFicha', dataUrl);

                if (success) {
                    this.fichaUrl = this.$wire.get('fichaDanoUrl');
                    this.fichaGuardada = true;
                    this.drawStarted = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Ficha guardada',
                        text: 'La imagen se guardó correctamente',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión al guardar la ficha', 'error');
            } finally {
                this.saving = false;
            }
        }
    }));
});
</script>