<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Administrador del sistema',
            'Cliente',
            'Vendedor',
            'Jefe de Taller',
            'Tecnico',
            'Almacen',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
    }
}
