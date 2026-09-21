<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shrink-0">
                    {!! $headerIcon ?? '<i class="fa-solid fa-cog text-sm"></i>' !!}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $title ?? 'Gestión CMS' }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $description ?? 'Administra el contenido del landing page' }}</p>
                </div>
            </div>
            <a href="{{ route('landing') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors border border-gray-200">
                <i class="fa-solid fa-eye text-xs"></i>
                Ver Landing
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => show = false, 4000)"
             class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
            <span class="flex-1 text-sm font-medium text-green-700">{{ session('success') }}</span>
            <button @click="show = false" class="text-green-500 hover:opacity-70"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => show = false, 4000)"
             class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
            <span class="flex-1 text-sm font-medium text-red-700">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-500 hover:opacity-70"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    {{-- Page Content --}}
    {{ $slot }}

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }

        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        [x-cloak] { display: none !important; }
    </style>

</div>