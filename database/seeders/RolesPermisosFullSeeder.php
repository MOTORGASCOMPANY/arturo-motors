<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

/**
 * Seeder completo de permisos, roles y asignaciones.
 * 
 * Generado desde la BD real del proyecto arturo-motors.
 * Ejecutar: php artisan db:seed --class=RolesPermisosFullSeeder
 * 
 * IMPORTANTE: Ejecutar despues de que las tablas permissions, roles,
 * model_has_roles, model_has_permissions existan (despues de migraciones de Spatie).
 */
class RolesPermisosFullSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 1. PERMISOS
        // ========================================
        $permisos = [
            // Usuarios
            'opciones.usuarios',
            'usuarios',
            'usuarios.roles',
            'usuarios.permisos',

            // Citas
            'opciones.citas',

            // Expedientes
            'opciones.expedientes',

            // Mantenimiento de tablas
            'opciones.mantenimientotables',

            // Conversiones
            'opciones.conversiones',
            'conversiones.asignar',
            'conversiones.mis-asignadas',
            'conversiones.entregas-pendientes',

            // Almacen
            'opciones.almacen',

            // Reportes
            'opciones.reportes',

            // RRHH
            'opciones.rrhh',
            'rrhh.contratos',
            'rrhh.planillas',

            // Caja
            'opciones.caja',

            // Servicios
            'opciones.servicios',

            // CMS
            'opciones.cms',
            'cms.contenido',
            'cms.servicios',
            'cms.pasos',
            'cms.contacto',
            'cms.redes',
            'cms.porque',

            // FISE
            'opciones.fise',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso, 'guard_name' => 'web']
            );
        }

        // ========================================
        // 2. ROLES CON SUS PERMISOS
        // ========================================

        // --- Administrador del sistema (TODOS los permisos) ---
        $admin = Role::firstOrCreate(['name' => 'Administrador del sistema', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // --- Cliente (sin permisos) ---
        Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

        // --- Vendedor ---
        $vendedor = Role::firstOrCreate(['name' => 'Vendedor', 'guard_name' => 'web']);
        $vendedor->syncPermissions([
            'opciones.citas',
            'opciones.expedientes',
            'opciones.servicios',
        ]);

        // --- Jefe de Taller (todo excepto usuarios) ---
        $jefe = Role::firstOrCreate(['name' => 'Jefe de Taller', 'guard_name' => 'web']);
        $jefe->syncPermissions([
            'opciones.citas',
            'opciones.expedientes',
            'opciones.conversiones',
            'opciones.servicios',
            'opciones.almacen',
            'opciones.rrhh',
            'opciones.reportes',
            'opciones.caja',
            'opciones.fise',
            'opciones.mantenimientotables',
            'opciones.cms',
            'conversiones.asignar',
            'conversiones.mis-asignadas',
            'conversiones.entregas-pendientes',
            'rrhh.contratos',
            'rrhh.planillas',
        ]);

        // --- Tecnico ---
        $tecnico = Role::firstOrCreate(['name' => 'Tecnico', 'guard_name' => 'web']);
        $tecnico->syncPermissions([
            'opciones.conversiones',
            'conversiones.mis-asignadas',
            'conversiones.entregas-pendientes',
        ]);

        // --- Almacen ---
        $almacen = Role::firstOrCreate(['name' => 'Almacen', 'guard_name' => 'web']);
        $almacen->syncPermissions([
            'opciones.almacen',
        ]);

        // --- Cajero (sin permisos) ---
        Role::firstOrCreate(['name' => 'Cajero', 'guard_name' => 'web']);

        // ========================================
        // 3. ASIGNACIONES USUARIO-ROL (de la BD real)
        // ========================================
        // user_id 1 (Felipe) = Administrador del sistema + Jefe de Taller
        // user_id 2 = Tecnico
        // user_id 3 (Felipe Vendedor) = Vendedor
        // user_id 4 (Felipe Tecnico) = Tecnico

        $userAdmin = User::find(1);
        if ($userAdmin) {
            $userAdmin->syncRoles(['Administrador del sistema', 'Jefe de Taller']);
        }

        $user2 = User::find(2);
        if ($user2) {
            $user2->syncRoles(['Tecnico']);
        }

        $userVendedor = User::find(3);
        if ($userVendedor) {
            $userVendedor->syncRoles(['Vendedor']);
        }

        $userTecnico = User::find(4);
        if ($userTecnico) {
            $userTecnico->syncRoles(['Tecnico']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
