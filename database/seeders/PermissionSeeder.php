<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Dashboard',
            'Usuario',
            'Medico',
            'Especialidad',
            'Paciente',
            'Procedencia',
            'Planificación',
            'Cita',
            'Reportes',
            'Morbilidad',
            'Patologia',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::create(['name' => 'administrador']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'usuario']);
        $role->givePermissionTo(['Dashboard', 'Cita']);

    }
}
