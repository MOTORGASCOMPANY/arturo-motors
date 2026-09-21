<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'Administrador del sistema')->first();

        $user1 = User::firstOrCreate(
            ['email' => 'felipeguerrero417@gmail.com'],
            [
                'name' => 'Felipe Guerrero',
                'password' => bcrypt('123456'),
            ]
        );
        if ($admin && !$user1->hasRole('Administrador del sistema')) {
            $user1->assignRole($admin);
        }

        User::firstOrCreate(
            ['email' => 'felipeguerrero419@gmail.com'],
            [
                'name' => 'Felipe Guerrero 419',
                'password' => bcrypt('123456'),
            ]
        );
    }
}
