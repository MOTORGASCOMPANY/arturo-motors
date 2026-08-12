<div>
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes modalFadeOut { from { opacity: 1; transform: scale(1) translateY(0); } to { opacity: 0; transform: scale(0.95) translateY(10px); } }
        @keyframes errorSlideIn { 0% { opacity: 0; transform: translateY(-20px); } 60% { transform: translateX(6px); } 80% { transform: translateX(-4px); } 100% { opacity: 1; transform: translateY(0) translateX(0); } }
        @keyframes successFlash { 0% { opacity: 0; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.02); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes imgRemove { to { opacity: 0; transform: scale(0.8); } }
        @keyframes barProgress { 0% { width: 0%; } 100% { width: 100%; } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Loading overlay --}}
    <div x-data="{ show: false, startTime: 0 }"
         x-show="show"
         x-cloak
         @uploading.window="show = true; startTime = Date.now()"
         @upload-done.window="setTimeout(() => { show = false }, Math.max(0, 5000 - (Date.now() - startTime)))"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="background: rgba(10, 15, 30, 0.92); backdrop-filter: blur(12px);"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden" style="width: 480px; animation: fadeUp 0.5s ease-out">

            {{-- Header --}}
            <div class="relative bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-6 py-4 overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full" style="background: repeating-linear-gradient(90deg, transparent, transparent 20px, rgba(255,255,255,0.03) 20px, rgba(255,255,255,0.03) 21px);"></div>
                </div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
                            <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg tracking-tight">Instalación GNV</h3>
                        <p class="text-slate-400 text-xs">Convirtiendo tu vehículo con la más alta tecnología</p>
                    </div>
                    <div class="ml-auto flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-emerald-400 text-[11px] font-semibold uppercase tracking-wider">En progreso</span>
                    </div>
                </div>
            </div>

            {{-- Workshop Scene --}}
            <div class="relative bg-gradient-to-b from-slate-50 to-slate-100 px-4 pt-4 pb-2">
                <svg width="448" height="240" viewBox="0 0 448 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="rounded-xl overflow-hidden">

                    {{-- === WORKSHOP BACKGROUND === --}}
                    {{-- Back wall --}}
                    <rect x="0" y="0" width="448" height="160" fill="#e8ecf1"/>
                    {{-- Wall texture lines --}}
                    <line x1="0" y1="40" x2="448" y2="40" stroke="#d1d5db" stroke-width="0.5" opacity="0.5"/>
                    <line x1="0" y1="80" x2="448" y2="80" stroke="#d1d5db" stroke-width="0.5" opacity="0.5"/>
                    <line x1="0" y1="120" x2="448" y2="120" stroke="#d1d5db" stroke-width="0.5" opacity="0.5"/>
                    {{-- Floor --}}
                    <rect x="0" y="160" width="448" height="80" fill="#94a3b8"/>
                    <rect x="0" y="160" width="448" height="3" fill="#64748b"/>
                    {{-- Floor tile pattern --}}
                    <line x1="0" y1="180" x2="448" y2="180" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="0" y1="200" x2="448" y2="200" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="0" y1="220" x2="448" y2="220" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="60" y1="160" x2="60" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="120" y1="160" x2="120" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="180" y1="160" x2="180" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="240" y1="160" x2="240" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="300" y1="160" x2="300" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="360" y1="160" x2="360" y2="240" stroke="#8896a7" stroke-width="0.5"/>
                    <line x1="420" y1="160" x2="420" y2="240" stroke="#8896a7" stroke-width="0.5"/>

                    {{-- === TOOLS ON WALL === --}}
                    {{-- Tool board --}}
                    <rect x="10" y="10" width="70" height="50" rx="3" fill="#78716c" stroke="#57534e" stroke-width="1"/>
                    <rect x="12" y="12" width="66" height="46" rx="2" fill="#44403c"/>
                    {{-- Wrenches on board --}}
                    <line x1="20" y1="20" x2="20" y2="45" stroke="#a8a29e" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="20" cy="47" r="3" fill="none" stroke="#a8a29e" stroke-width="1.5"/>
                    <line x1="30" y1="18" x2="30" y2="48" stroke="#d6d3d1" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="30" cy="50" r="2.5" fill="none" stroke="#d6d3d1" stroke-width="1.5"/>
                    <line x1="40" y1="22" x2="40" y2="42" stroke="#a8a29e" stroke-width="3" stroke-linecap="round"/>
                    <line x1="50" y1="15" x2="50" y2="50" stroke="#d6d3d1" stroke-width="2" stroke-linecap="round"/>
                    <rect x="58" y="18" width="12" height="3" rx="1" fill="#a8a29e"/>
                    <rect x="60" y="24" width="8" height="3" rx="1" fill="#d6d3d1"/>
                    <rect x="62" y="30" width="6" height="3" rx="1" fill="#a8a29e"/>

                    {{-- Oil barrel --}}
                    <ellipse cx="35" cy="155" rx="14" ry="5" fill="#1f2937"/>
                    <rect x="21" y="105" width="28" height="50" rx="2" fill="#1f2937"/>
                    <ellipse cx="35" cy="105" rx="14" ry="5" fill="#374151"/>
                    <rect x="21" y="115" width="28" height="2" fill="#ef4444"/>
                    <text x="35" y="135" font-size="6" fill="#9ca3af" text-anchor="middle" font-weight="bold">OIL</text>

                    {{-- Fire extinguisher --}}
                    <rect x="415" y="20" width="10" height="30" rx="3" fill="#ef4444"/>
                    <rect x="413" y="15" width="14" height="8" rx="2" fill="#dc2626"/>
                    <rect x="418" y="10" width="4" height="6" rx="1" fill="#78716c"/>
                    <circle cx="420" cy="8" r="2" fill="#9ca3af"/>

                    {{-- === CAR LIFT STRUCTURE === --}}
                    {{-- Left post --}}
                    <rect x="80" y="50" width="12" height="110" rx="2" fill="#475569"/>
                    <rect x="76" y="46" width="20" height="10" rx="2" fill="#64748b"/>
                    <rect x="78" y="56" width="16" height="4" rx="1" fill="#334155"/>
                    <rect x="78" y="76" width="16" height="4" rx="1" fill="#334155"/>
                    <rect x="78" y="96" width="16" height="4" rx="1" fill="#334155"/>
                    {{-- Right post --}}
                    <rect x="356" y="50" width="12" height="110" rx="2" fill="#475569"/>
                    <rect x="352" y="46" width="20" height="10" rx="2" fill="#64748b"/>
                    <rect x="354" y="56" width="16" height="4" rx="1" fill="#334155"/>
                    <rect x="354" y="76" width="16" height="4" rx="1" fill="#334155"/>
                    <rect x="354" y="96" width="16" height="4" rx="1" fill="#334155"/>
                    {{-- Lift arms --}}
                    <rect x="92" y="100" width="272" height="6" rx="2" fill="#64748b"/>
                    <rect x="92" y="100" width="272" height="2" rx="1" fill="#94a3b8"/>
                    {{-- Hydraulic pistons --}}
                    <rect x="88" y="106" width="8" height="40" rx="2" fill="#334155"/>
                    <rect x="360" y="106" width="8" height="40" rx="2" fill="#334155"/>
                    <rect x="86" y="140" width="12" height="6" rx="1" fill="#475569"/>
                    <rect x="358" y="140" width="12" height="6" rx="1" fill="#475569"/>

                    {{-- === CAR ON LIFT === --}}
                    <g transform="translate(120, 52)">
                        {{-- Shadow on lift --}}
                        <ellipse cx="105" cy="55" rx="95" ry="5" fill="#000" opacity="0.08"/>
                        {{-- Undercarriage --}}
                        <rect x="10" y="44" width="190" height="8" rx="2" fill="#334155"/>
                        {{-- Exhaust pipe --}}
                        <rect x="5" y="48" width="30" height="3" rx="1.5" fill="#78716c"/>
                        <rect x="2" y="47" width="5" height="5" rx="2" fill="#57534e"/>
                        {{-- Car body lower --}}
                        <path d="M8 38 L12 32 L198 32 L202 38 L202 46 L8 46 Z" fill="#2563eb"/>
                        {{-- Car body upper --}}
                        <path d="M20 32 L32 16 L168 16 L190 30 L198 32 L20 32 Z" fill="#3b82f6"/>
                        {{-- Roof --}}
                        <path d="M45 18 L58 4 L148 4 L162 18 Z" fill="#1d4ed8"/>
                        {{-- Roof rack --}}
                        <rect x="60" y="2" width="80" height="3" rx="1" fill="#1e40af"/>
                        <rect x="65" y="0" width="4" height="5" rx="1" fill="#1e40af"/>
                        <rect x="90" y="0" width="4" height="5" rx="1" fill="#1e40af"/>
                        <rect x="115" y="0" width="4" height="5" rx="1" fill="#1e40af"/>
                        <rect x="140" y="0" width="4" height="5" rx="1" fill="#1e40af"/>
                        {{-- Windows --}}
                        <path d="M48 19 L58 8 L100 8 L100 19 Z" fill="#bae6fd" opacity="0.85"/>
                        <path d="M104 19 L104 8 L145 8 L158 19 Z" fill="#bae6fd" opacity="0.85"/>
                        {{-- Window reflections --}}
                        <path d="M52 17 L60 10 L75 10 L68 17 Z" fill="white" opacity="0.2"/>
                        <path d="M108 17 L115 10 L130 10 L125 17 Z" fill="white" opacity="0.15"/>
                        {{-- Window pillars --}}
                        <rect x="100" y="6" width="4" height="14" fill="#1d4ed8"/>
                        {{-- Door lines --}}
                        <line x1="75" y1="18" x2="75" y2="42" stroke="#1e40af" stroke-width="0.8"/>
                        <line x1="130" y1="18" x2="130" y2="42" stroke="#1e40af" stroke-width="0.8"/>
                        {{-- Door handles --}}
                        <rect x="80" y="28" width="10" height="2.5" rx="1" fill="#93c5fd" opacity="0.6"/>
                        <rect x="135" y="28" width="10" height="2.5" rx="1" fill="#93c5fd" opacity="0.6"/>
                        {{-- Side mirror --}}
                        <rect x="16" y="22" width="8" height="5" rx="2" fill="#1d4ed8"/>
                        <rect x="14" y="23" width="3" height="3" rx="1" fill="#bae6fd" opacity="0.7"/>
                        {{-- Headlights --}}
                        <rect x="196" y="28" width="8" height="8" rx="3" fill="#fef3c7"/>
                        <rect x="196" y="28" width="8" height="8" rx="3" fill="#fbbf24" opacity="0.5">
                            <animate attributeName="opacity" values="0.3;0.7;0.3" dur="2s" repeatCount="indefinite"/>
                        </rect>
                        <rect x="200" y="30" width="4" height="4" rx="1" fill="white" opacity="0.4"/>
                        {{-- Taillights --}}
                        <rect x="4" y="34" width="6" height="6" rx="2" fill="#ef4444"/>
                        <rect x="4" y="34" width="6" height="6" rx="2" fill="#dc2626" opacity="0.4">
                            <animate attributeName="opacity" values="0.2;0.6;0.2" dur="2.5s" repeatCount="indefinite"/>
                        </rect>
                        {{-- Bumper --}}
                        <rect x="2" y="42" width="204" height="3" rx="1" fill="#94a3b8"/>
                        {{-- License plate area --}}
                        <rect x="80" y="42" width="50" height="4" rx="1" fill="white"/>
                        <text x="105" y="46" font-size="2.5" fill="#1f2937" text-anchor="middle" font-family="monospace">ARTURO</text>
                        {{-- Wheels --}}
                        <circle cx="45" cy="50" r="10" fill="#1f2937"/>
                        <circle cx="45" cy="50" r="7" fill="#374151"/>
                        <circle cx="45" cy="50" r="4" fill="#4b5563"/>
                        <circle cx="45" cy="50" r="1.5" fill="#9ca3af"/>
                        {{-- Wheel spokes --}}
                        <line x1="45" y1="41" x2="45" y2="59" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="36" y1="50" x2="54" y2="50" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="38" y1="43" x2="52" y2="57" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="52" y1="43" x2="38" y2="57" stroke="#6b7280" stroke-width="0.8"/>
                        <circle cx="165" cy="50" r="10" fill="#1f2937"/>
                        <circle cx="165" cy="50" r="7" fill="#374151"/>
                        <circle cx="165" cy="50" r="4" fill="#4b5563"/>
                        <circle cx="165" cy="50" r="1.5" fill="#9ca3af"/>
                        <line x1="165" y1="41" x2="165" y2="59" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="156" y1="50" x2="174" y2="50" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="158" y1="43" x2="172" y2="57" stroke="#6b7280" stroke-width="0.8"/>
                        <line x1="172" y1="43" x2="158" y2="57" stroke="#6b7280" stroke-width="0.8"/>
                        {{-- GNV Tank under car --}}
                        <rect x="55" y="46" width="60" height="12" rx="6" fill="#059669"/>
                        <rect x="55" y="46" width="60" height="12" rx="6" stroke="#047857" stroke-width="1"/>
                        <rect x="60" y="49" width="15" height="6" rx="2" fill="#047857"/>
                        <text x="90" y="56" font-size="5" fill="white" text-anchor="middle" font-weight="bold" font-family="Arial">GNV</text>
                        {{-- Valve on tank --}}
                        <rect x="82" y="43" width="6" height="5" rx="1.5" fill="#047857"/>
                        <circle cx="85" cy="42" r="2" fill="#065f46"/>
                        {{-- Tank gauge --}}
                        <circle cx="130" cy="52" r="4" fill="white" stroke="#047857" stroke-width="0.8"/>
                        <line x1="130" y1="52" x2="130" y2="49" stroke="#ef4444" stroke-width="0.8" stroke-linecap="round">
                            <animateTransform attributeName="transform" type="rotate" values="-30,130,52;30,130,52;-30,130,52" dur="2s" repeatCount="indefinite"/>
                        </line>
                        <circle cx="130" cy="52" r="1" fill="#1f2937"/>
                    </g>

                    {{-- === MECHANIC 1: Under car on creeper (IMPROVED) === --}}
                    <g transform="translate(165, 120)">
                        {{-- Creeper board --}}
                        <rect x="-20" y="48" width="80" height="6" rx="3" fill="#57534e"/>
                        <rect x="-18" y="50" width="76" height="2" rx="1" fill="#78716c"/>
                        <circle cx="-12" cy="56" r="3.5" fill="#44403c" stroke="#292524" stroke-width="0.8"/>
                        <circle cx="-12" cy="56" r="1.5" fill="#57534e"/>
                        <circle cx="58" cy="56" r="3.5" fill="#44403c" stroke="#292524" stroke-width="0.8"/>
                        <circle cx="58" cy="56" r="1.5" fill="#57534e"/>
                        <circle cx="22" cy="56" r="3.5" fill="#44403c" stroke="#292524" stroke-width="0.8"/>
                        <circle cx="22" cy="56" r="1.5" fill="#57534e"/>

                        {{-- Legs (bent at knee, realistic) --}}
                        <path d="M5 42 Q8 46 10 48 L18 48 Q20 46 22 42" fill="#1f2937" stroke="#111827" stroke-width="0.3"/>
                        <path d="M28 42 Q30 46 32 48 L40 48 Q42 46 44 42" fill="#1f2937" stroke="#111827" stroke-width="0.3"/>
                        {{-- Boots --}}
                        <rect x="6" y="46" width="14" height="4" rx="2" fill="#44403c"/>
                        <rect x="30" y="46" width="14" height="4" rx="2" fill="#44403c"/>

                        {{-- Torso (orange work shirt with details) --}}
                        <path d="M0 22 Q-2 30 0 42 L44 42 Q46 30 44 22 Z" fill="#ea580c"/>
                        <path d="M0 22 Q-2 30 0 42 L44 42 Q46 30 44 22 Z" fill="none" stroke="#c2410c" stroke-width="0.5"/>
                        {{-- Shirt collar --}}
                        <path d="M16 22 L22 26 L28 22" fill="#c2410c"/>
                        {{-- Shirt pocket --}}
                        <rect x="30" y="28" width="8" height="6" rx="1" fill="#c2410c" opacity="0.5"/>
                        <line x1="32" y1="29" x2="36" y2="29" stroke="#ea580c" stroke-width="0.5"/>
                        {{-- Belt --}}
                        <rect x="0" y="39" width="44" height="3" rx="1" fill="#292524"/>
                        <rect x="18" y="38.5" width="8" height="4" rx="1" fill="#78716c"/>

                        {{-- Arms reaching up to car undercarriage --}}
                        {{-- Left arm --}}
                        <path d="M2 24 Q-4 16 0 6" fill="none" stroke="#d4a574" stroke-width="5" stroke-linecap="round">
                            <animate attributeName="d" values="M2 24 Q-4 16 0 6;M2 24 Q-6 14 -2 4;M2 24 Q-4 16 0 6" dur="0.6s" repeatCount="indefinite"/>
                        </path>
                        {{-- Left hand --}}
                        <circle cx="0" cy="5" r="3" fill="#d4a574">
                            <animate attributeName="cy" values="5;3;5" dur="0.6s" repeatCount="indefinite"/>
                        </circle>
                        {{-- Right arm --}}
                        <path d="M42 24 Q48 16 44 6" fill="none" stroke="#d4a574" stroke-width="5" stroke-linecap="round">
                            <animate attributeName="d" values="M42 24 Q48 16 44 6;M42 24 Q50 14 46 4;M42 24 Q48 16 44 6" dur="0.6s" repeatCount="indefinite"/>
                        </path>
                        {{-- Right hand --}}
                        <circle cx="44" cy="5" r="3" fill="#d4a574">
                            <animate attributeName="cy" values="5;3;5" dur="0.6s" repeatCount="indefinite"/>
                        </circle>

                        {{-- Wrench in right hand --}}
                        <g>
                            <animateTransform attributeName="transform" type="rotate" values="-15,44,5;15,44,5;-15,44,5" dur="0.6s" repeatCount="indefinite"/>
                            <rect x="36" y="1" width="16" height="3" rx="1" fill="#9ca3af"/>
                            <circle cx="54" cy="2.5" r="4" fill="none" stroke="#9ca3af" stroke-width="2"/>
                            <circle cx="54" cy="2.5" r="2" fill="#78716c"/>
                        </g>

                        {{-- Head (realistic, turned slightly looking up) --}}
                        <ellipse cx="48" cy="18" rx="9" ry="10" fill="#d4a574"/>
                        {{-- Ear --}}
                        <ellipse cx="57" cy="18" rx="2.5" ry="3" fill="#c4956a"/>
                        <ellipse cx="57" cy="18" rx="1.5" ry="2" fill="#d4a574"/>
                        {{-- Hair --}}
                        <path d="M39 14 Q48 4 57 14" fill="#292524"/>
                        <path d="M39 14 Q39 10 42 12" fill="#292524"/>
                        <path d="M57 14 Q57 10 54 12" fill="#292524"/>
                        <path d="M40 12 Q44 8 48 12" fill="#292524"/>
                        {{-- Cap --}}
                        <path d="M37 12 Q48 4 59 12" fill="#1f2937"/>
                        <rect x="37" y="11" width="22" height="4" rx="2" fill="#1f2937"/>
                        <rect x="35" y="11" width="7" height="3.5" rx="1.5" fill="#1f2937"/>
                        {{-- Cap logo --}}
                        <rect x="44" y="12" width="8" height="2" rx="0.5" fill="#ea580c" opacity="0.7"/>
                        {{-- Safety glasses --}}
                        <rect x="40" y="15" width="6" height="4" rx="1.5" fill="white" opacity="0.3"/>
                        <rect x="48" y="15" width="6" height="4" rx="1.5" fill="white" opacity="0.3"/>
                        <line x1="46" y1="17" x2="48" y2="17" stroke="#292524" stroke-width="0.6"/>
                        <rect x="39" y="14.5" width="16" height="5" rx="2" fill="none" stroke="#292524" stroke-width="0.5" opacity="0.3"/>
                        {{-- Eye (looking up) --}}
                        <circle cx="43" cy="17" r="1.2" fill="white"/>
                        <circle cx="43" cy="16.5" r="0.8" fill="#292524"/>
                        <circle cx="51" cy="17" r="1.2" fill="white"/>
                        <circle cx="51" cy="16.5" r="0.8" fill="#292524"/>
                        {{-- Nose --}}
                        <path d="M47 19 Q48 21 49 19" fill="none" stroke="#c4956a" stroke-width="0.8"/>
                        {{-- Mouth (focused expression) --}}
                        <line x1="45" y1="22" x2="51" y2="22" stroke="#a87c4f" stroke-width="0.8"/>
                    </g>

                    {{-- === MECHANIC 2: Standing with clipboard === --}}
                    <g transform="translate(370, 65)">
                        {{-- Body --}}
                        <rect x="10" y="28" width="26" height="35" rx="4" fill="#2563eb"/>
                        <path d="M14 28 L23 34 L32 28" fill="#1d4ed8"/>
                        {{-- AM logo --}}
                        <text x="23" y="46" font-size="5" fill="white" text-anchor="middle" font-weight="bold" font-family="Arial">AM</text>
                        {{-- Arms --}}
                        <line x1="10" y1="32" x2="2" y2="44" stroke="#d4a574" stroke-width="4" stroke-linecap="round"/>
                        <line x1="36" y1="32" x2="44" y2="38" stroke="#d4a574" stroke-width="4" stroke-linecap="round"/>
                        {{-- Clipboard --}}
                        <rect x="42" y="30" width="14" height="20" rx="2" fill="#f5f5f4" stroke="#a8a29e" stroke-width="0.8"/>
                        <rect x="46" y="28" width="6" height="4" rx="1" fill="#78716c"/>
                        <line x1="45" y1="36" x2="53" y2="36" stroke="#d6d3d1" stroke-width="0.8"/>
                        <line x1="45" y1="40" x2="53" y2="40" stroke="#d6d3d1" stroke-width="0.8"/>
                        <line x1="45" y1="44" x2="50" y2="44" stroke="#d6d3d1" stroke-width="0.8"/>
                        <circle cx="47" cy="48" r="1" fill="#22c55e"/>
                        {{-- Head --}}
                        <circle cx="23" cy="18" r="10" fill="#d4a574"/>
                        {{-- Hair --}}
                        <path d="M13 14 Q23 4 33 14" fill="#292524"/>
                        <path d="M13 14 Q13 10 17 12" fill="#292524"/>
                        <path d="M33 14 Q33 10 29 12" fill="#292524"/>
                        {{-- Safety glasses --}}
                        <rect x="15" y="15" width="6" height="4" rx="1.5" fill="white" opacity="0.35"/>
                        <rect x="23" y="15" width="6" height="4" rx="1.5" fill="white" opacity="0.35"/>
                        <line x1="21" y1="17" x2="23" y2="17" stroke="#292524" stroke-width="0.6"/>
                        <rect x="14" y="14.5" width="16" height="5" rx="2" fill="none" stroke="#292524" stroke-width="0.6" opacity="0.4"/>
                        {{-- Mouth --}}
                        <path d="M19 22 Q23 25 27 22" fill="none" stroke="#a87c4f" stroke-width="0.8"/>
                        {{-- Legs --}}
                        <rect x="12" y="61" width="9" height="18" rx="2" fill="#1f2937"/>
                        <rect x="25" y="61" width="9" height="18" rx="2" fill="#1f2937"/>
                        {{-- Boots --}}
                        <rect x="10" y="77" width="12" height="5" rx="2" fill="#44403c"/>
                        <rect x="24" y="77" width="12" height="5" rx="2" fill="#44403c"/>
                    </g>

                    {{-- === SPARKS from wrench under car === --}}
                    <g transform="translate(195, 115)">
                        <circle r="2" fill="#fbbf24">
                            <animate attributeName="opacity" values="0;1;0" dur="0.35s" repeatCount="indefinite"/>
                            <animate attributeName="cx" values="0;-8;-14" dur="0.35s" repeatCount="indefinite"/>
                            <animate attributeName="cy" values="0;-10;-18" dur="0.35s" repeatCount="indefinite"/>
                        </circle>
                        <circle r="1.5" fill="#f59e0b">
                            <animate attributeName="opacity" values="0;1;0" dur="0.35s" repeatCount="indefinite" begin="0.1s"/>
                            <animate attributeName="cx" values="4;10;16" dur="0.35s" repeatCount="indefinite" begin="0.1s"/>
                            <animate attributeName="cy" values="2;-6;-14" dur="0.35s" repeatCount="indefinite" begin="0.1s"/>
                        </circle>
                        <circle r="1" fill="#fbbf24">
                            <animate attributeName="opacity" values="0;1;0" dur="0.35s" repeatCount="indefinite" begin="0.2s"/>
                            <animate attributeName="cx" values="-3;-10;-16" dur="0.35s" repeatCount="indefinite" begin="0.2s"/>
                            <animate attributeName="cy" values="3;-5;-12" dur="0.35s" repeatCount="indefinite" begin="0.2s"/>
                        </circle>
                        <circle r="1.8" fill="#fcd34d">
                            <animate attributeName="opacity" values="0;1;0" dur="0.35s" repeatCount="indefinite" begin="0.3s"/>
                            <animate attributeName="cx" values="6;12;18" dur="0.35s" repeatCount="indefinite" begin="0.3s"/>
                            <animate attributeName="cy" values="0;-8;-16" dur="0.35s" repeatCount="indefinite" begin="0.3s"/>
                        </circle>
                        <circle r="0.8" fill="#fbbf24">
                            <animate attributeName="opacity" values="0;1;0" dur="0.35s" repeatCount="indefinite" begin="0.15s"/>
                            <animate attributeName="cx" values="-5;-12;-18" dur="0.35s" repeatCount="indefinite" begin="0.15s"/>
                            <animate attributeName="cy" values="1;-7;-15" dur="0.35s" repeatCount="indefinite" begin="0.15s"/>
                        </circle>
                    </g>

                    {{-- === TOOL CART === --}}
                    <g transform="translate(15, 115)">
                        <rect x="0" y="0" width="38" height="28" rx="3" fill="#dc2626"/>
                        <rect x="2" y="2" width="34" height="8" rx="1.5" fill="#b91c1c"/>
                        <rect x="2" y="12" width="34" height="8" rx="1.5" fill="#b91c1c"/>
                        <rect x="2" y="22" width="34" height="4" rx="1" fill="#991b1b"/>
                        <circle cx="8" cy="10" r="1.5" fill="#fbbf24"/>
                        <circle cx="30" cy="10" r="1.5" fill="#fbbf24"/>
                        <circle cx="8" cy="20" r="1.5" fill="#fbbf24"/>
                        <circle cx="30" cy="20" r="1.5" fill="#fbbf24"/>
                        <rect x="36" y="-4" width="3" height="12" rx="1" fill="#78716c"/>
                        <rect x="33" y="-6" width="9" height="3" rx="1" fill="#78716c"/>
                        <circle cx="6" cy="32" r="3.5" fill="#1f2937" stroke="#44403c" stroke-width="0.8"/>
                        <circle cx="6" cy="32" r="1.5" fill="#4b5563"/>
                        <circle cx="32" cy="32" r="3.5" fill="#1f2937" stroke="#44403c" stroke-width="0.8"/>
                        <circle cx="32" cy="32" r="1.5" fill="#4b5563"/>
                        <rect x="5" y="-8" width="14" height="3" rx="1" fill="#64748b"/>
                        <rect x="22" y="-10" width="5" height="8" rx="1" fill="#94a3b8"/>
                        <rect x="29" y="-7" width="4" height="5" rx="1" fill="#64748b"/>
                    </g>

                    {{-- === GNV TANK STORAGE === --}}
                    <g transform="translate(395, 80)">
                        <rect x="0" y="0" width="28" height="60" rx="4" fill="#059669"/>
                        <rect x="0" y="0" width="28" height="60" rx="4" stroke="#047857" stroke-width="1.2"/>
                        <rect x="5" y="6" width="18" height="10" rx="2" fill="#047857"/>
                        <text x="14" y="14" font-size="6" fill="white" text-anchor="middle" font-weight="bold" font-family="Arial">GNV</text>
                        <rect x="9" y="-6" width="10" height="10" rx="2" fill="#64748b"/>
                        <circle cx="14" cy="-1" r="2.5" fill="#475569"/>
                        <circle cx="14" cy="38" r="8" fill="white" stroke="#047857" stroke-width="1"/>
                        <circle cx="14" cy="38" r="6" fill="#f0fdf4"/>
                        <line x1="14" y1="38" x2="14" y2="32" stroke="#ef4444" stroke-width="1" stroke-linecap="round">
                            <animateTransform attributeName="transform" type="rotate" values="-35,14,38;35,14,38;-35,14,38" dur="2.5s" repeatCount="indefinite"/>
                        </line>
                        <circle cx="14" cy="38" r="1.5" fill="#1f2937"/>
                        <text x="14" y="50" font-size="4" fill="#047857" text-anchor="middle" font-family="monospace">PSI</text>
                    </g>

                    {{-- === OIL STAINS ON FLOOR === --}}
                    <ellipse cx="200" cy="195" rx="25" ry="6" fill="#44403c" opacity="0.12"/>
                    <ellipse cx="280" cy="215" rx="15" ry="4" fill="#44403c" opacity="0.08"/>

                    {{-- === OVERHEAD LIGHT === --}}
                    <rect x="210" y="2" width="28" height="6" rx="2" fill="#d1d5db"/>
                    <rect x="215" y="8" width="18" height="4" rx="1" fill="#fef3c7"/>
                    <line x1="215" y1="12" x2="210" y2="40" stroke="#fef3c7" stroke-width="0.3" opacity="0.4"/>
                    <line x1="233" y1="12" x2="238" y2="40" stroke="#fef3c7" stroke-width="0.3" opacity="0.4"/>
                </svg>
            </div>

            {{-- Status text --}}
            <div class="px-6 py-4 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Instalando sistema GNV</h4>
                        <p class="text-[11px] text-gray-400">Técnicos especializados trabajando</p>
                    </div>
                    <div class="flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1 rounded-full">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-emerald-600 text-[10px] font-semibold">ACTIVO</span>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-orange-400 via-amber-500 to-orange-500 rounded-full" style="animation: barProgress 5s ease-in-out"></div>
                </div>
                <p class="text-[10px] text-gray-400 text-center mt-2">Procesando imagen... esto tomará unos segundos</p>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-edit text-blue-600"></i>Gestionar Contenido
        </h4>
        <span class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-full">{{ $pageTitle }}</span>
    </div>

    {{-- Transient success message --}}
    @if($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.clearSuccessMessage() }, 3000)"
             x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i>{{ $successMessage }}</span>
            <button @click="show = false; $wire.clearSuccessMessage()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session()->has('success') && !$successMessage)
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="space-y-6 m-4">
        @foreach ($sections as $section)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.08 }}s both">
                <div class="flex flex-col lg:flex-row">

                    {{-- IZQUIERDA: Preview de cómo se ve en la web --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-green-400"></div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Así se ve en la página</p>
                        </div>
                        <x-section-preview :section="$section" :refreshKey="$refreshKey" :highlight="$highlightSection === $section['key']" />
                    </div>

                    {{-- DERECHA: Info + imágenes + acciones --}}
                    <div class="lg:w-1/2 p-6">
                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-gray-900">{{ $section['title'] }}</h6>
                                    <p class="text-gray-400 text-xs">Clave: <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono">{{ $section['key'] }}</code></p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $section['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                                {{ $section['is_active'] ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>

                        {{-- Info --}}
                        @if($section['subtitle'] || $section['description'])
                            <div class="mb-4 space-y-1">
                                @if($section['subtitle'])
                                    <p class="text-sm"><span class="font-medium text-gray-700">Subtítulo:</span> <span class="text-gray-500">{{ Str::limit($section['subtitle'], 80) }}</span></p>
                                @endif
                                @if($section['description'])
                                    <p class="text-sm"><span class="font-medium text-gray-700">Descripción:</span> <span class="text-gray-500">{{ Str::limit($section['description'], 80) }}</span></p>
                                @endif
                            </div>
                        @endif

                        {{-- Imágenes actuales --}}
                        @php
                            $imageLimits = ['hero' => 5, 'about' => 2];
                            $maxImages = $imageLimits[$section['key']] ?? 0;
                            $currentCount = count($section['media_items'] ?? []);
                            $canUpload = $maxImages > 0 && $currentCount < $maxImages;
                        @endphp
                        @if($maxImages > 0 && $currentCount > 0)
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    Imágenes ({{ $currentCount }}/{{ $maxImages }})
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($section['media_items'] as $pm)
                                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                            <img src="{{ asset('storage/' . $pm['media']['file_path']) }}" class="w-full h-20 object-cover">
                                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1">
                                                <a href="{{ asset('storage/' . $pm['media']['file_path']) }}" target="_blank" class="bg-white text-gray-700 text-[10px] font-semibold px-2 py-1 rounded-md hover:bg-gray-100 flex items-center gap-1">
                                                    <i class="fa-solid fa-expand"></i>Ver
                                                </a>
                                                <button onclick="window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Eliminar imagen', message: '¿Seguro que querés eliminar esta imagen? Esta acción no se puede deshacer.', action: { componentId: $wire.__instance.id, method: 'removeMedia', params: [{{ $pm['id'] }}] } } }))" class="bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded-md hover:bg-red-600 flex items-center gap-1 transition-all">
                                                    <i class="fa-solid fa-trash"></i>Borrar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Upload - solo hero (max 5) y about (max 2) --}}
                        @if($maxImages > 0)
                            <div class="mb-4">
                                @if($canUpload)
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                        <input type="file" id="file-{{ $section['id'] }}" accept="image/*" class="hidden">
                                        <div class="flex gap-2 items-center">
                                            <button onclick="document.getElementById('file-{{ $section['id'] }}').click()"
                                                    class="flex-1 text-sm border border-dashed border-gray-300 rounded-lg px-3 py-2 bg-white text-left text-gray-500 hover:bg-gray-100 hover:border-gray-400 transition-all cursor-pointer">
                                                <i class="fa-solid fa-cloud-arrow-up mr-1 text-gray-400"></i><span id="file-label-{{ $section['id'] }}">Elegí una imagen...</span>
                                            </button>
                                            <button onclick="jsUpload({{ $section['id'] }}, this)"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold transition-all shadow-sm whitespace-nowrap">
                                                <i class="fa-solid fa-upload mr-1"></i>Subir
                                            </button>
                                        </div>
                                        <div id="upload-progress-{{ $section['id'] }}" class="hidden mt-2">
                                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div id="upload-bar-{{ $section['id'] }}" class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-gray-400 mt-1.5">Max {{ $maxImages }} imágenes · JPG, PNG o WebP · 5MB max</p>
                                    </div>
                                @else
                                    <div class="bg-green-50 rounded-xl p-3 border border-green-200 text-center">
                                        <p class="text-green-600 text-xs font-medium">
                                            <i class="fa-solid fa-circle-check mr-1"></i>Límite alcanzado ({{ $currentCount }}/{{ $maxImages }})
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Formulario inline SIEMPRE visible --}}
                        @php $sd = $sectionData[$section['id']] ?? ['title' => $section['title'], 'subtitle' => $section['subtitle'], 'description' => $section['description'], 'is_active' => $section['is_active']]; @endphp
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 space-y-3">
                            @if ($errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-lg text-xs">
                                    @foreach ($errors->all() as $error)
                                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-circle-exclamation mt-0.5"></i>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Título</label>
                                <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white" wire:model="sectionData.{{ $section['id'] }}.title" wire:focus="editSection({{ $section['id'] }})">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Subtítulo</label>
                                <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white" wire:model="sectionData.{{ $section['id'] }}.subtitle" wire:focus="editSection({{ $section['id'] }})">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción</label>
                                <textarea class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white" rows="3" wire:model="sectionData.{{ $section['id'] }}.description" wire:focus="editSection({{ $section['id'] }})"></textarea>
                            </div>
                            <div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300" wire:model="sectionData.{{ $section['id'] }}.is_active" wire:focus="editSection({{ $section['id'] }})">
                                    <span class="text-xs font-medium text-gray-600">Activa</span>
                                </label>
                            </div>
                            <div class="flex justify-end pt-1">
                                <button type="button" class="px-4 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-xs font-semibold transition-all shadow-sm"
                                        wire:click="saveSection({{ $section['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:target="saveSection">
                                    <span wire:loading.remove wire:target="saveSection"><i class="fa-solid fa-check mr-1"></i>Guardar</span>
                                    <span wire:loading wire:target="saveSection">Guardando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // File label update on select
        document.addEventListener('change', function(e) {
            if (e.target.type === 'file' && e.target.files.length) {
                var id = e.target.id.replace('file-', '');
                var label = document.getElementById('file-label-' + id);
                if (label) label.textContent = e.target.files[0].name;
            }
        }, true);

        // Pure JS upload - zero Livewire re-render
        function jsUpload(sectionId, btn) {
            var fileInput = document.getElementById('file-' + sectionId);
            if (!fileInput.files.length) {
                window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Imagen requerida', message: 'Elegí una imagen primero antes de subir.', action: null } }));
                return;
            }

            // Show loading overlay
            var startTime = Date.now();
            window.dispatchEvent(new CustomEvent('uploading'));

            var file = fileInput.files[0];
            var formData = new FormData();
            formData.append('file', file);
            formData.append('section_id', sectionId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            var progress = document.getElementById('upload-progress-' + sectionId);
            var bar = document.getElementById('upload-bar-' + sectionId);
            progress.classList.remove('hidden');
            bar.style.width = '30%';
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Subiendo...';

            fetch('{{ route("cms.upload-media") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                bar.style.width = '100%';
                if (data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check mr-1"></i>¡Listo!';
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-green-600');
                    fileInput.value = '';
                    var label = document.getElementById('file-label-' + sectionId);
                    if (label) label.textContent = 'Elegí una imagen...';
                    // Keep overlay visible for minimum 5 seconds, then reload
                    setTimeout(function() {
                        window.dispatchEvent(new CustomEvent('upload-done'));
                        setTimeout(function() { window.location.reload(); }, 500);
                    }, Math.max(0, 5000 - (Date.now() - startTime)));
                } else {
                    window.dispatchEvent(new CustomEvent('upload-done'));
                    window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Error', message: data.error || 'Error al subir imagen', action: null } }));
                    progress.classList.add('hidden');
                    bar.style.width = '0%';
                    btn.innerHTML = '<i class="fa-solid fa-upload mr-1"></i>Subir';
                    btn.disabled = false;
                }
            })
            .catch(function() {
                window.dispatchEvent(new CustomEvent('upload-done'));
                window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Error', message: 'Error de conexión. Revisá tu red y probá de nuevo.', action: null } }));
                progress.classList.add('hidden');
                bar.style.width = '0%';
                btn.innerHTML = '<i class="fa-solid fa-upload mr-1"></i>Subir';
                btn.disabled = false;
            });
        }
    </script>
</div>
