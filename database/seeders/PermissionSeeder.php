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
            'Patologia',
            'Atender Cita',
            'Gestión de Cita',
            'Médicos inactivos',
            'Auditoría',
            'Liberar Historia',
            'Editar atencion',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'administrador']);
        $role->givePermissionTo(Permission::all());

        $role = Role::firstOrCreate(['name' => 'usuario']);
        $role->givePermissionTo(['Dashboard', 'Cita']);

    }
}
