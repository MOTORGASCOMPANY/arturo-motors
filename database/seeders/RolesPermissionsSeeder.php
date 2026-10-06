<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Permisos organizados por módulo/funcionalidad
     *
     * @var array<string, array<int, array{name: string, descripcion: string}>>
     */
    private const PERMISOS = [
        'usuarios' => [
            ['name' => 'opciones.usuarios', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de usuarios'],
            ['name' => 'usuarios', 'descripcion' => 'Administrar usuarios'],
            ['name' => 'usuarios.roles', 'descripcion' => 'Administrar roles de usuario'],
            ['name' => 'usuarios.permisos', 'descripcion' => 'Administrar permisos de rol'],
        ],
        'citas' => [
            ['name' => 'opciones.citas', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de citas'],
        ],
        'expedientes' => [
            ['name' => 'opciones.expedientes', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de expedientes'],
        ],
        'mantenimiento' => [
            ['name' => 'opciones.mantenimientotables', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de mantenimiento de tablas'],
        ],
        'conversiones' => [
            ['name' => 'opciones.conversiones', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de conversiones'],
            ['name' => 'conversiones.asignar', 'descripcion' => 'Asignar técnico, ve todas las órdenes creada'],
            ['name' => 'conversiones.mis-asignadas', 'descripcion' => 'Mis conversiones asignadas filtra por tecnico'],
            ['name' => 'conversiones.entregas-pendientes', 'descripcion' => 'Entrega y cobro, orden de conversión'],
        ],
        'almacen' => [
            ['name' => 'opciones.almacen', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de almacén'],
        ],
        'reportes' => [
            ['name' => 'opciones.reportes', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de reportes'],
        ],
        'rrhh' => [
            ['name' => 'opciones.rrhh', 'descripcion' => 'Puede ver en el menú de navegación las opciones de recursos humanos'],
            ['name' => 'rrhh.contratos', 'descripcion' => 'Lista empleados, crea contratos, vacaciones y documentos.'],
            ['name' => 'rrhh.planillas', 'descripcion' => 'Lista y crea planillas según el periodo'],
        ],
        'caja' => [
            ['name' => 'opciones.caja', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de caja'],
        ],
        'servicios' => [
            ['name' => 'opciones.servicios', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de servicios'],
        ],
        'cms' => [
            ['name' => 'opciones.cms', 'descripcion' => 'Puede ver en el menú de navegación las opciones del modulo de cms'],
            ['name' => 'cms.contenido', 'descripcion' => 'contenido cms'],
            ['name' => 'cms.servicios', 'descripcion' => 'servicios cms'],
            ['name' => 'cms.pasos', 'descripcion' => 'pasos cms'],
            ['name' => 'cms.contacto', 'descripcion' => 'contacto cms'],
            ['name' => 'cms.redes', 'descripcion' => 'redes cms'],
            ['name' => 'cms.porque', 'descripcion' => 'porque cms'],
        ],
        'fise' => [
            ['name' => 'opciones.fise', 'descripcion' => ''],
        ],
        'ordenes' => [
            ['name' => 'ordenes.listado', 'descripcion' => 'Ver la tarjeta de órdenes de hoy en el dashboard y acceder al listado general de órdenes'],
        ],
        'dashboard' => [
            ['name' => 'dashboard.metricas', 'descripcion' => 'Ver las métricas de conversiones (hoy/semana/mes) en el dashboard'],
            ['name' => 'dashboard.graficos', 'descripcion' => 'Ver los gráficos de ingresos y conversiones (últimos 30 días) en el dashboard'],
        ],
    ];

    /**
     *Roles con sus permisos asignados.
     *
     *REGLA: "Administrador del sistema" y "Jefe de Taller" ven EXACTAMENTE lo mismo,
     * con una única diferencia: el módulo de Usuarios y Roles es exclusivo del admin.
     *
     * @var array<string, array{name: string, permissions: array<int, string>}>
     */
    private const ROLES = [
        'admin' => [
            'name' => 'Administrador del sistema',
            'permissions' => [
                // usuarios (exclusivo del admin)
                'opciones.usuarios', 'usuarios', 'usuarios.roles', 'usuarios.permisos',
                // citas
                'opciones.citas',
                // expedientes
                'opciones.expedientes',
                // mantenimiento
                'opciones.mantenimientotables',
                // conversiones
                'opciones.conversiones', 'conversiones.asignar', 'conversiones.mis-asignadas', 'conversiones.entregas-pendientes',
                // almacen
                'opciones.almacen',
                // reportes
                'opciones.reportes',
                // rrhh
                'opciones.rrhh', 'rrhh.contratos', 'rrhh.planillas',
                // caja
                'opciones.caja',
                // servicios
                'opciones.servicios',
                // cms
                'opciones.cms', 'cms.contenido', 'cms.servicios', 'cms.pasos', 'cms.contacto', 'cms.redes', 'cms.porque',
                // fise
                'opciones.fise',
                // ordenes + dashboard
                'ordenes.listado', 'dashboard.metricas', 'dashboard.graficos',
            ],
        ],
        'cliente' => [
            'name' => 'Cliente',
            'permissions' => [],
        ],
        'vendedor' => [
            'name' => 'Vendedor',
            'permissions' => [
                'opciones.citas',
                'opciones.expedientes',
                'opciones.servicios',
                'ordenes.listado',
                'dashboard.metricas',
            ],
        ],
        'jefe_taller' => [
            'name' => 'Jefe de Taller',
            // Mismo listado que 'admin' SIN el módulo de usuarios.
            // Si agregas un permiso arriba, agrégalo aquí también (excepto usuarios.*).
            'permissions' => [
                'opciones.citas',
                'opciones.expedientes',
                'opciones.mantenimientotables',
                'opciones.conversiones', 'conversiones.asignar', 'conversiones.mis-asignadas', 'conversiones.entregas-pendientes',
                'opciones.almacen',
                'opciones.reportes',
                'opciones.rrhh', 'rrhh.contratos', 'rrhh.planillas',
                'opciones.caja',
                'opciones.servicios',
                'opciones.cms', 'cms.contenido', 'cms.servicios', 'cms.pasos', 'cms.contacto', 'cms.redes', 'cms.porque',
                'opciones.fise',
                'ordenes.listado', 'dashboard.metricas', 'dashboard.graficos',
            ],
        ],
        'tecnico' => [
            'name' => 'Tecnico',
            'permissions' => [
                'opciones.conversiones',
                'conversiones.mis-asignadas',
                'conversiones.entregas-pendientes',
                'ordenes.listado',
                'dashboard.metricas',
            ],
        ],
        'almacen' => [
            'name' => 'Almacen',
            'permissions' => [
                'opciones.almacen',
                'ordenes.listado',
            ],
        ],
        'cajero' => [
            'name' => 'Cajero',
            'permissions' => [
                'opciones.caja',
                'opciones.fise',
                // P7: entrega y cobro de conversiones (documentado en routes/web.php)
                'opciones.conversiones',
                'conversiones.entregas-pendientes',
                'ordenes.listado',
                'dashboard.metricas',
            ],
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear todos los permisos
        $this->command->info('Creando permisos...');
        foreach (self::PERMISOS as $modulo => $permisos) {
            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(
                    ['name' => $permiso['name'], 'guard_name' => 'web'],
                    ['descripcion' => $permiso['descripcion']]
                );
            }
        }
        $this->command->info('Permisos creados: ' . Permission::count());

        // 2. Crear roles y asignar permisos
        $this->command->info('Creando roles y asignando permisos...');
        foreach (self::ROLES as $key => $rolData) {
            $role = Role::firstOrCreate(
                ['name' => $rolData['name'], 'guard_name' => 'web']
            );

            $permissions = Permission::whereIn('name', $rolData['permissions'])->get();
            $role->syncPermissions($permissions);

            $this->command->info("Rol '{$rolData['name']}' → {$permissions->count()} permisos");
        }

        $this->command->info('Roles creados: ' . Role::count());
        $this->command->info('¡Seeder completado!');
    }
}