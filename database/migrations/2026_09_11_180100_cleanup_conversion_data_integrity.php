<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // ═══════════════════════════════════════════════════════════════
            // ORDER 12 — conversion_completada
            // Fix: kit parent (id=21) should be 'consumido' not 'en_stock'
            // Fix: reports #16 and #17 should be 'resuelto'
            // ═══════════════════════════════════════════════════════════════

            // Kit 21 was the kit assigned to order 12. After conversion,
            // finalizar() incorrectly returned it to 'en_stock'. Mark as 'consumido'.
            DB::table('items_serializados')
                ->where('id', 21)
                ->update([
                    'estado' => 'consumido',
                    'service_order_id' => 12,  // keep link to the order
                ]);

            // Reports #16 and #17 for order 12 — conversion is complete, mark resolved
            DB::table('reporte_piezas_no_encajadas')
                ->whereIn('id', [16, 17])
                ->where('service_order_id', 12)
                ->update(['estado' => 'resuelto']);

            // ═══════════════════════════════════════════════════════════════
            // ORDER 13 — en_conversion (INCOMPLETE)
            // Problems:
            //   - Kit 151 assigned to order 13, but only has 2 children
            //   - Those 2 children (239, 278) were actually from kit 150
            //     (wrongly reassigned by RegistrarItemsKit)
            //   - Missing ALL quantity components
            // Fix: unassign kit 151, clean up items, reset order to aprobado_conversion
            // ═══════════════════════════════════════════════════════════════

            // 1. Unlink items 239 and 278 from order 13 and kit 151
            //    They were from kit 150 (Vaporizador + Tanque extracted for report #17)
            //    Put them back as loose pieces in stock
            DB::table('items_serializados')
                ->whereIn('id', [239, 278])
                ->update([
                    'estado' => 'en_stock',
                    'service_order_id' => null,
                    'kit_padre_id' => null,
                    'atributos' => json_encode([
                        'tipo' => 'serial',
                        'notas' => 'Devuelto a stock — originalmente de kit 150, mal asignado a orden 13',
                    ]),
                ]);

            // 2. Unassign kit 151 from order 13 — put back to stock
            DB::table('items_serializados')
                ->where('id', 151)
                ->update([
                    'estado' => 'en_stock',
                    'service_order_id' => null,
                ]);

            // 3. Reset order 13 to pre-assignment state
            DB::table('service_orders')
                ->where('id', 13)
                ->update([
                    'estado' => 'aprobado_conversion',
                    'fecha_inicio_conversion' => null,
                    'fecha_fin_conversion' => null,
                ]);

        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            // Reverse order 13
            DB::table('service_orders')
                ->where('id', 13)
                ->update([
                    'estado' => 'en_conversion',
                    'fecha_inicio_conversion' => '2026-09-11 11:53:36',
                ]);

            DB::table('items_serializados')
                ->where('id', 151)
                ->update([
                    'estado' => 'asignado',
                    'service_order_id' => 13,
                ]);

            DB::table('items_serializados')
                ->whereIn('id', [239, 278])
                ->update([
                    'estado' => 'asignado',
                    'service_order_id' => 13,
                    'kit_padre_id' => 151,
                ]);

            // Reverse order 12
            DB::table('items_serializados')
                ->where('id', 21)
                ->update(['estado' => 'en_stock']);

            DB::table('reporte_piezas_no_encajadas')
                ->whereIn('id', [16, 17])
                ->update(['estado' => 'kit_abierto']);
        });
    }
};
