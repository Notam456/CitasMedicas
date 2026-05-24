<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'Dashboard',
            'Usuarios',
            'Médicos',
            'Especialidad',
            'Paciente',
            'Procedencia',
            'Planificación',
            'Citas',
            'Reportes',
            'Morbilidad',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign existing permissions
        $role = Role::create(['name' => 'administrador']);
        $role->givePermissionTo(Permission::all());

        Role::create(['name' => 'usuario']);
    }
}
