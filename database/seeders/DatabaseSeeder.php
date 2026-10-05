<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // SEEDERS INDISPENSABLES (base de la app)
        // ==========================================
        $this->call([
            // Catálogos maestros
            ServicesSeeder::class,           // Servicios: Conversión GNV/GLP, Garantía, etc.
            CategoriasAlmacenSeeder::class,  // Categorías: Reductores, Tanques, Kits, etc.
            TipoDocumentoSeeder::class,      // Tipos doc: DNI, Antecedentes, CV, Contrato

            // Autorización (reemplaza a RoleSeeder)
            RolesPermissionsSeeder::class,   // 29 permisos + 7 roles + 51 relaciones pivote

            // Sedes reales (producción)
            SedeSeeder::class,               // Callao, Ancón, Villa María
        ]);

        // ==========================================
        // SEEDERS DE PRUEBA (comentados - solo desarrollo)
        // ==========================================
        // $this->call([
        //     ClienteSeeder::class,
        //     VehiculoSeeder::class,
        //     ClienteVehiculoSeeder::class,
        //     ServiceOrderSeeder::class,
        //     SesionCajaSeeder::class,
        //     ProductosGnvSeeder::class,
        //     DatosPruebaAlmacenSeeder::class,
        // ]);
    }
}
