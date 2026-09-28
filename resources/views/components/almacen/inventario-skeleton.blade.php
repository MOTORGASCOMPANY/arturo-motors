{{-- Skeleton loaders para cada tipo de tarjeta/gráfico --}}
@if ($type === 'kpi')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 animate-pulse">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-slate-100"></div>
            <div class="h-5 w-24 rounded bg-slate-100"></div>
        </div>
        <div class="h-12 w-20 rounded bg-slate-100 mb-2"></div>
        <div class="h-4 w-28 rounded bg-slate-100"></div>
    </div>

@elseif ($type === 'gauge')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 animate-pulse">
        <div class="h-5 w-32 rounded bg-slate-100 mb-4"></div>
        <div class="flex justify-center">
            <div class="h-24 w-24 rounded-full bg-slate-100"></div>
        </div>
        <div class="mt-4 h-4 w-20 rounded bg-slate-100 mx-auto"></div>
    </div>

@elseif ($type === 'bar')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 animate-pulse">
        <div class="h-5 w-40 rounded bg-slate-100 mb-6"></div>
        <div class="h-48 w-full rounded bg-slate-100"></div>
    </div>

@elseif ($type === 'list')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 animate-pulse">
        <div class="h-5 w-36 rounded bg-slate-100 mb-4"></div>
        @for ($i = 0; $i < 4; $i++)
            <div class="flex items-center gap-3 py-2">
                <div class="h-8 w-8 rounded bg-slate-100"></div>
                <div class="flex-1">
                    <div class="h-4 w-3/4 rounded bg-slate-100"></div>
                    <div class="h-3 w-1/3 rounded bg-slate-100 mt-1"></div>
                </div>
            </div>
        @endfor
    </div>
@endif