<?php

namespace Database\Seeders;

use App\Models\CategoriaAlmacen;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosGnvSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CATEGORÍAS — Separadas por generación
        $catEquipo3ra = CategoriaAlmacen::updateOrCreate(
            ['nombre' => 'Equipo 3RA'],
            ['es_serializado' => true, 'es_kit' => true, 'esquema_atributos' => ['procedencia']]
        );

        $catEquipo5ta = CategoriaAlmacen::updateOrCreate(
            ['nombre' => 'Equipo 5TA'],
            ['es_serializado' => true, 'es_kit' => true, 'esquema_atributos' => ['procedencia']]
        );

        $catTanques = CategoriaAlmacen::updateOrCreate(
            ['nombre' => 'Tanques'],
            ['es_serializado' => true, 'es_kit' => false, 'esquema_atributos' => ['serie', 'marca', 'capacidad', 'produce']]
        );

        $catRepuestos = CategoriaAlmacen::updateOrCreate(
            ['nombre' => 'Repuestos'],
            ['es_serializado' => false, 'es_kit' => false, 'esquema_atributos' => null]
        );

        // 2. PRODUCTOS — KITS (uno por generación)
        $kit3ra = Producto::updateOrCreate(
            ['nombre' => 'Kit Instalación 3RA'],
            ['categoria_id' => $catEquipo3ra->id, 'atributos' => ['procedencia' => 'local'], 'activo' => true]
        );

        $kit5ta = Producto::updateOrCreate(
            ['nombre' => 'Kit Instalación 5TA'],
            ['categoria_id' => $catEquipo5ta->id, 'atributos' => ['procedencia' => 'local'], 'activo' => true]
        );

        // 3. PRODUCTOS — COMPONENTES 3RA
        $comp3ra = [];

        $comp3ra['vaporizador'] = Producto::updateOrCreate(
            ['nombre' => 'Vaporizador'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['toma_llenado'] = Producto::updateOrCreate(
            ['nombre' => 'Toma de Llenado'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['valvula_cilindro'] = Producto::updateOrCreate(
            ['nombre' => 'Válvula de Cilindro'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['manometro'] = Producto::updateOrCreate(
            ['nombre' => 'Manómetro'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['regulador_alta'] = Producto::updateOrCreate(
            ['nombre' => 'Regulador de Alta'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['electrovalvula'] = Producto::updateOrCreate(
            ['nombre' => 'Electrovalvula Ramal'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['conmutador'] = Producto::updateOrCreate(
            ['nombre' => 'Conmutador'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['terminal_hembra'] = Producto::updateOrCreate(
            ['nombre' => 'Terminal Hembra'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['relay'] = Producto::updateOrCreate(
            ['nombre' => 'Relay (Inyectado)'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );

        // Mangueras 3RA
        $comp3ra['manguera_gas'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 3/4 Gas (1MT)'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['manguera_gasolina'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 5/16 Gasolina (1MT)'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['manguera_agua'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 5/16 Agua (1.5MT)'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['corrugado'] = Producto::updateOrCreate(
            ['nombre' => 'Corrugado'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['caneria'] = Producto::updateOrCreate(
            ['nombre' => 'Cañería de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['conos'] = Producto::updateOrCreate(
            ['nombre' => 'Conos de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['t_agua'] = Producto::updateOrCreate(
            ['nombre' => 'T de Agua'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['niples'] = Producto::updateOrCreate(
            ['nombre' => 'Niples de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['tapones'] = Producto::updateOrCreate(
            ['nombre' => 'Tapones'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['bolsa_venteo'] = Producto::updateOrCreate(
            ['nombre' => 'Bolsa de Venteo'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['abrazadera_grande'] = Producto::updateOrCreate(
            ['nombre' => 'Abrazadera 16/27'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp3ra['abrazadera_chica'] = Producto::updateOrCreate(
            ['nombre' => 'Abrazadera 10/16'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );

        // 4. PRODUCTOS — COMPONENTES 5TA
        $comp5ta = [];

        $comp5ta['vaporizador'] = Producto::updateOrCreate(
            ['nombre' => 'Vaporizador'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['toma_llenado'] = Producto::updateOrCreate(
            ['nombre' => 'Toma de Llenado'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['valvula_cilindro'] = Producto::updateOrCreate(
            ['nombre' => 'Válvula de Cilindro'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['electronica_aeb'] = Producto::updateOrCreate(
            ['nombre' => 'Electrónica AEB'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['computadora'] = Producto::updateOrCreate(
            ['nombre' => 'Computadora'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['filtro_gas'] = Producto::updateOrCreate(
            ['nombre' => 'Filtro de Gas'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['sensor'] = Producto::updateOrCreate(
            ['nombre' => 'Sensor'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['ramal'] = Producto::updateOrCreate(
            ['nombre' => 'Ramal Eléctrico'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['conmutador'] = Producto::updateOrCreate(
            ['nombre' => 'Conmutador'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['manometro'] = Producto::updateOrCreate(
            ['nombre' => 'Manómetro'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['porta_fusible'] = Producto::updateOrCreate(
            ['nombre' => 'Porta Fusible'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['inyectores'] = Producto::updateOrCreate(
            ['nombre' => 'Inyectores'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );

        // Mangueras 5TA
        $comp5ta['manguera_gas'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 1/2 Gas'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['manguera_inyectores'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 3/16 Inyectores'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['manguera_agua'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 5/16 Agua'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['manguera_vacio'] = Producto::updateOrCreate(
            ['nombre' => 'Manguera 3/16 Vacío'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['corrugado'] = Producto::updateOrCreate(
            ['nombre' => 'Corrugado'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['caneria'] = Producto::updateOrCreate(
            ['nombre' => 'Cañería de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['conos'] = Producto::updateOrCreate(
            ['nombre' => 'Conos de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['t_agua'] = Producto::updateOrCreate(
            ['nombre' => 'T de Agua'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['niples'] = Producto::updateOrCreate(
            ['nombre' => 'Niples de 6'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['tapones'] = Producto::updateOrCreate(
            ['nombre' => 'Tapones'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['bolsa_venteo'] = Producto::updateOrCreate(
            ['nombre' => 'Bolsa de Venteo'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );
        $comp5ta['abrazaderas'] = Producto::updateOrCreate(
            ['nombre' => 'Abrazaderas'],
            ['categoria_id' => $catRepuestos->id, 'activo' => true]
        );

        // 5. COMPONENTES DEL KIT 3RA
        $componentes3ra = [
            [$comp3ra['vaporizador']->id, 1],
            [$comp3ra['toma_llenado']->id, 1],
            [$comp3ra['valvula_cilindro']->id, 1],
            [$comp3ra['manometro']->id, 1],
            [$comp3ra['regulador_alta']->id, 1],
            [$comp3ra['electrovalvula']->id, 1],
            [$comp3ra['conmutador']->id, 1],
            [$comp3ra['terminal_hembra']->id, 1],
            [$comp3ra['relay']->id, 1],
            [$comp3ra['manguera_gas']->id, 1],
            [$comp3ra['manguera_gasolina']->id, 1],
            [$comp3ra['manguera_agua']->id, 1],
            [$comp3ra['corrugado']->id, 1],
            [$comp3ra['caneria']->id, 1],
            [$comp3ra['conos']->id, 4],
            [$comp3ra['t_agua']->id, 2],
            [$comp3ra['niples']->id, 4],
            [$comp3ra['tapones']->id, 1],
            [$comp3ra['bolsa_venteo']->id, 1],
            [$comp3ra['abrazadera_grande']->id, 8],
            [$comp3ra['abrazadera_chica']->id, 4],
        ];

        // 6. COMPONENTES DEL KIT 5TA
        $componentes5ta = [
            [$comp5ta['vaporizador']->id, 1],
            [$comp5ta['toma_llenado']->id, 1],
            [$comp5ta['valvula_cilindro']->id, 1],
            [$comp5ta['electronica_aeb']->id, 1],
            [$comp5ta['computadora']->id, 1],
            [$comp5ta['filtro_gas']->id, 1],
            [$comp5ta['sensor']->id, 1],
            [$comp5ta['ramal']->id, 1],
            [$comp5ta['conmutador']->id, 1],
            [$comp5ta['manometro']->id, 1],
            [$comp5ta['porta_fusible']->id, 1],
            [$comp5ta['inyectores']->id, 1],
            [$comp5ta['manguera_gas']->id, 1],
            [$comp5ta['manguera_inyectores']->id, 1],
            [$comp5ta['manguera_agua']->id, 1],
            [$comp5ta['manguera_vacio']->id, 1],
            [$comp5ta['corrugado']->id, 1],
            [$comp5ta['caneria']->id, 1],
            [$comp5ta['conos']->id, 4],
            [$comp5ta['t_agua']->id, 2],
            [$comp5ta['niples']->id, 4],
            [$comp5ta['tapones']->id, 2],
            [$comp5ta['bolsa_venteo']->id, 1],
            [$comp5ta['abrazaderas']->id, 12],
        ];

        // 7. INSERTAR COMPONENTES
        DB::table('kit_componentes')
            ->whereIn('producto_kit_id', [$kit3ra->id, $kit5ta->id])
            ->delete();

        foreach ($componentes3ra as [$componenteId, $cantidad]) {
            DB::table('kit_componentes')->insert([
                'producto_kit_id' => $kit3ra->id,
                'producto_componente_id' => $componenteId,
                'cantidad_esperada' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($componentes5ta as [$componenteId, $cantidad]) {
            DB::table('kit_componentes')->insert([
                'producto_kit_id' => $kit5ta->id,
                'producto_componente_id' => $componenteId,
                'cantidad_esperada' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
