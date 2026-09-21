<?php

namespace Database\Seeders;

use App\Models\CategoriaAlmacen;
use App\Models\Cliente;
use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\ServiceOrder;
use App\Models\Sede;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatosPruebaAlmacenSeeder extends Seeder
{
    private int $sedeCallao = 4;
    private int $sedeSantaAnita = 5;
    private int $sedeAncon = 6;

    public function run(): void
    {
        // 1. USUARIOS
        $almacenero = User::firstOrCreate(
            ['email' => 'almacen@arturomotors.com'],
            [
                'name' => 'Carlos Almacén',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@arturomotors.com'],
            [
                'name' => 'Juan Técnico',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $cajero = User::firstOrCreate(
            ['email' => 'cajero@arturomotors.com'],
            [
                'name' => 'María Cajero',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@arturomotors.com'],
            [
                'name' => 'Pedro Vendedor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. CLIENTES
        $clientes = [];
        foreach (['Carlos Pérez', 'Ana García', 'Luis Martínez', 'Rosa López', 'Miguel Sánchez'] as $nombre) {
            [$apellido, $resto] = explode(' ', $nombre . ' X');
            $clientes[] = Cliente::firstOrCreate(
                ['documento' => rand(10000000, 99999999)],
                [
                    'nombre' => explode(' ', $nombre)[0],
                    'apellido' => explode(' ', $nombre)[1] ?? '',
                    'telefono' => '9' . rand(10000000, 99999999),
                    'email' => strtolower(str_replace(' ', '.', $nombre)) . '@test.com',
                    'tipo_persona' => 'natural',
                ]
            );
        }

        // 3. VEHÍCULOS
        $vehiculos = [];
        $placas = ['CTF056', 'CTF078', 'CTF091', 'CTF102', 'CTF115'];
        foreach ($placas as $i => $placa) {
            $vehiculo = Vehiculo::firstOrCreate(
                ['placa' => $placa],
                [
                    'marca' => ['Toyota', 'Hyundai', 'Kia', 'Nissan', 'Mitsubishi'][$i],
                    'modelo' => ['Corolla', 'Accent', 'Rio', 'Sentra', 'L200'][$i],
                    'anio' => rand(2015, 2023),
                    'color' => ['Blanco', 'Negro', 'Gris', 'Azul', 'Rojo'][$i],
                    'combustible' => 'gasolina',
                ]
            );
            // Asociar cliente al vehículo
            $vehiculo->clientes()->syncWithoutDetaching([
                $clientes[$i]->id => ['es_principal' => true, 'relacion' => 'propietario']
            ]);
            $vehiculos[] = $vehiculo;
        }

        // 4. SERVICIOS
        $servicioGnv = Service::firstOrCreate(
            ['nombre' => 'Conversión a GNV'],
            ['precio_base' => 2500, 'tipo' => 'conversion', 'activo' => true]
        );

        // 5. ÓRDENES DE SERVICIO
        $ordenes = [];
        foreach ($vehiculos as $i => $vehiculo) {
            $estado = ['creada', 'aprobado_conversion', 'en_conversion', 'conversion_completada', 'creada'][$i];
            $orden = ServiceOrder::firstOrCreate(
                ['vehiculo_id' => $vehiculo->id, 'service_id' => $servicioGnv->id],
                [
                    'cliente_id' => $clientes[$i]->id,
                    'tecnico_id' => $tecnico->id,
                    'creado_por' => $vendedor->id,
                    'estado' => $estado,
                    'precio_lista' => 2500,
                    'precio_final' => 2500,
                    'fecha_inicio_conversion' => $estado === 'en_conversion' ? now()->subHours(2) : null,
                ]
            );
            $ordenes[] = $orden;
        }

        // 6. PRODUCTOS (asegurar que existan)
        // Ejecutar el seeder de productos si no existen
        if (Producto::count() === 0) {
            $this->call(ProductosGnvSeeder::class);
        }

        $kit5ta = Producto::where('nombre', 'Kit 5ta Generacion')->first();
        $kitSuper = Producto::where('nombre', 'Kit Super')->first();
        $redTomasetto = Producto::where('nombre', 'Reductor TOMASETTO AT12')->first();
        $tanque40 = Producto::where('nombre', 'Tanque 40L')->first();
        $tanque60 = Producto::where('nombre', 'Tanque 60L')->first();
        $ecu = Producto::where('nombre', 'ECU 48 Pines')->first();
        $valvulaLlenado = Producto::where('nombre', 'Valvula de Llenado')->first();
        $valvulaSeguridad = Producto::where('nombre', 'Valvula de Seguridad')->first();
        $pernos = Producto::where('nombre', 'Pernos, Abrazaderas y Bridas')->first();
        $computadora = Producto::where('nombre', 'Computadora')->first();

        // 7. KITS SELLADOS EN CALLAO
        $kitsCallao = [];
        for ($i = 1; $i <= 5; $i++) {
            $kit = ItemSerializado::create([
                'producto_id' => $kit5ta->id,
                'serie' => 'KIT-CAL-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'estado' => 'en_stock',
                'sede_id' => $this->sedeCallao,
                'atributos' => ['proveedor' => 'MOCAVIN', 'fecha_recepcion' => now()->toDateString()],
            ]);
            $kitsCallao[] = $kit;
        }

        // Kit Super en Callao
        $kitSuperItem = ItemSerializado::create([
            'producto_id' => $kitSuper->id,
            'serie' => 'KIT-CAL-S001',
            'estado' => 'en_stock',
            'sede_id' => $this->sedeCallao,
            'atributos' => ['proveedor' => 'AUTO TOP', 'fecha_recepcion' => now()->toDateString()],
        ]);

        // 8. KITS SELLADOS EN SANTA ANITA
        for ($i = 1; $i <= 3; $i++) {
            ItemSerializado::create([
                'producto_id' => $kit5ta->id,
                'serie' => 'KIT-SAN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'estado' => 'en_stock',
                'sede_id' => $this->sedeSantaAnita,
                'atributos' => ['proveedor' => 'UNIGAS', 'fecha_recepcion' => now()->toDateString()],
            ]);
        }

        // 9. KITS SELLADOS EN ANCON
        for ($i = 1; $i <= 2; $i++) {
            ItemSerializado::create([
                'producto_id' => $kit5ta->id,
                'serie' => 'KIT-ANC-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'estado' => 'en_stock',
                'sede_id' => $this->sedeAncon,
                'atributos' => ['proveedor' => "D'WILLIAMS", 'fecha_recepcion' => now()->toDateString()],
            ]);
        }

        // 10. PIEZAS SUELTAS EN CALLAO (de kits abiertos)
        $piezasSueltas = [
            ['producto_id' => $redTomasetto->id, 'serie' => 'RED-CAL-001'],
            ['producto_id' => $redTomasetto->id, 'serie' => 'RED-CAL-002'],
            ['producto_id' => $tanque40->id, 'serie' => 'TAN-CAL-001'],
            ['producto_id' => $tanque60->id, 'serie' => 'TAN-CAL-002'],
            ['producto_id' => $computadora->id, 'serie' => 'COM-CAL-001'],
        ];

        foreach ($piezasSueltas as $pieza) {
            ItemSerializado::create([
                'producto_id' => $pieza['producto_id'],
                'serie' => $pieza['serie'],
                'estado' => 'en_stock',
                'sede_id' => $this->sedeCallao,
                'atributos' => ['origen' => 'kit_abierto'],
            ]);
        }

        // 11. REPUESTOS POR CANTIDAD (CALLAO)
        $repuestos = [
            [$ecu->id, 10],
            [$valvulaLlenado->id, 20],
            [$valvulaSeguridad->id, 15],
            [$pernos->id, 100],
        ];

        foreach ($repuestos as [$productoId, $cantidad]) {
            ProductoStockSede::updateOrCreate(
                ['producto_id' => $productoId, 'sede_id' => $this->sedeCallao],
                ['cantidad' => $cantidad]
            );
        }

        // 12. REPUESTOS EN SANTA ANITA
        ProductoStockSede::updateOrCreate(
            ['producto_id' => $pernos->id, 'sede_id' => $this->sedeSantaAnita],
            ['cantidad' => 50]
        );

        // 13. ÓRDENES CON KIT ASIGNADO (para probar flujo)
        // Asignar kit a la orden en estado "en_conversion"
        if (isset($ordenes[2]) && $ordenes[2]->estado === 'en_conversion') {
            $kitParaAsignar = $kitsCallao[0];
            $kitParaAsignar->update([
                'estado' => 'asignado',
                'service_order_id' => $ordenes[2]->id,
            ]);

            // Crear componentes del kit como items individuales
            $componentes = [
                ['producto_id' => $redTomasetto->id, 'serie' => 'RED-ASN-001'],
                ['producto_id' => $tanque40->id, 'serie' => 'TAN-ASN-001'],
                ['producto_id' => $ecu->id, 'serie' => 'ECU-ASN-001'],
            ];

            foreach ($componentes as $comp) {
                ItemSerializado::create([
                    'producto_id' => $comp['producto_id'],
                    'serie' => $comp['serie'],
                    'estado' => 'asignado',
                    'sede_id' => $this->sedeCallao,
                    'service_order_id' => $ordenes[2]->id,
                    'kit_padre_id' => $kitParaAsignar->id,
                    'atributos' => ['origen' => 'kit_abierto'],
                ]);
            }
        }

        // RESUMEN
        $this->command->info('✅ Datos de prueba creados:');
        $this->command->info("   - Usuarios: almacén, técnico, cajero, vendedor");
        $this->command->info("   - Clientes: 5");
        $this->command->info("   - Vehículos: 5 (CTF056-CTF115)");
        $this->command->info("   - Órdenes: 5 (diferentes estados)");
        $this->command->info("   - Kits sellados: 5 en Callao, 3 en Santa Anita, 2 en Ancon");
        $this->command->info("   - Piezas sueltas: 5 en Callao");
        $this->command->info("   - Repuestos: en las 3 sedes");
        $this->command->info("   - 1 orden con kit asignado (CTF091)");
    }
}
