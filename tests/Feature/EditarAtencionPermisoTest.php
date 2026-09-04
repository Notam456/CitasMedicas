<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditarAtencionPermisoTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioSinPermiso(): User
    {
        $usuario = User::create([
            'name' => 'SinPermiso',
            'email' => 'sinpermiso@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($usuario);

        return $usuario;
    }

    private function usuarioConPermiso(): User
    {
        $usuario = User::create([
            'name' => 'ConPermiso',
            'email' => 'conpermiso@test.com',
            'password' => bcrypt('password'),
        ]);
        $role = Role::firstOrCreate(['name' => 'editable-test']);
        Permission::firstOrCreate(['name' => 'Editar atencion']);
        $role->givePermissionTo('Editar atencion');
        $usuario->assignRole($role);
        $this->actingAs($usuario);

        return $usuario;
    }

    public function test_sin_permiso_no_accede_al_get_edit(): void
    {
        $this->usuarioSinPermiso();
        $this->get(route('diagnosticos.edit', 1))->assertForbidden();
    }

    public function test_sin_permiso_no_accede_al_put_update(): void
    {
        $this->usuarioSinPermiso();
        $this->put(route('diagnosticos.update', 1))->assertForbidden();
    }

    public function test_con_permiso_pasa_la_barrera_de_la_ruta_edit(): void
    {
        $this->usuarioConPermiso();
        // Al no existir la cita 999999, el controlador responde 404, demostrando
        // que el permiso ya pasó (no 403).
        $this->get(route('diagnosticos.edit', 999999))->assertNotFound();
    }

    public function test_con_permiso_pasa_la_barrera_de_la_ruta_update(): void
    {
        $this->usuarioConPermiso();
        $this->put(route('diagnosticos.update', 999999), ['diagnostico_libre' => 'x'])->assertNotFound();
    }
}
