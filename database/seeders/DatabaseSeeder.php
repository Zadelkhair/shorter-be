<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // create role
        $adminRole = Role::create(['name' => 'admin']);

        // create permissions
        $permissions = [
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // create admin user
        $admin = \App\Models\User::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('admin@password'),
        ]);

        // assign role to admin
        $admin->assignRole($adminRole);

        // create simple user
        $user = \App\Models\User::create([
            'name' => 'user',
            'email' => 'user@email.com',
            'password' => bcrypt('user@password'),
        ]);


    }
}
