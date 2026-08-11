<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class CmsRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos CMS
        $cmsPermissions = [
            'opciones.cms',
            'cms.contenido',
            'cms.servicios',
            'cms.pasos',
            'cms.contacto',
            'cms.redes',
            'cms.porque',
        ];

        foreach ($cmsPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Crear rol admin con todos los permisos CMS
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($cmsPermissions);

        // Asignar rol admin al primer usuario (o el que vos quieras)
        $user = User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
