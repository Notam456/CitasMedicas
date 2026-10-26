<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProcedenciaEliminacionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $role = Role::firstOrCreate(['name' => 'administrador']);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => 'Procedencia']));
        $admin->assignRole($role);

        return $admin;
    }

    /**
     * @return array{0:int,1:int,2:int} estado, municipio, parroquia
     */
    private function ubicacion(): array
    {
        $estado = DB::table('estados')->insertGetId(['nombre' => 'Test Estado', 'created_at' => now(), 'updated_at' => now()]);
        $municipio = DB::table('municipios')->insertGetId(['estado_id' => $estado, 'nombre' => 'Test Municipio', 'created_at' => now(), 'updated_at' => now()]);
        $parroquia = DB::table('parroquias')->insertGetId(['municipio_id' => $municipio, 'nombre' => 'Test Parroquia', 'created_at' => now(), 'updated_at' => now()]);

        return [$estado, $municipio, $parroquia];
    }

    private function paciente(int $parroquiaId): void
    {
        DB::table('pacientes')->insert([
            'parroquia_id' => $parroquiaId,
            'nombre' => 'Ana',
            'apellido' => 'Pérez',
            'cedula' => 'V-12345678',
            'rif' => 'J-12345678-1',
            'fecha_nacimiento' => '1990-01-01',
            'telefono' => '04141234567',
            'direccion' => 'Calle 1',
            'sexo' => 'Femenino',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_no_elimina_estado_con_municipios(): void
    {
        $this->actingAs($this->admin());
        [$estado] = $this->ubicacion();

        $this->delete(route('estados.destroy', $estado))->assertRedirect();

        $this->assertDatabaseHas('estados', ['id' => $estado]);
    }

    public function test_no_elimina_municipio_con_parroquias(): void
    {
        $this->actingAs($this->admin());
        [, $municipio] = $this->ubicacion();

        $this->delete(route('municipios.destroy', $municipio))->assertRedirect();

        $this->assertDatabaseHas('municipios', ['id' => $municipio]);
    }

    public function test_no_elimina_parroquia_con_pacientes(): void
    {
        $this->actingAs($this->admin());
        [, , $parroquia] = $this->ubicacion();
        $this->paciente($parroquia);

        $this->delete(route('parroquias.destroy', $parroquia))->assertRedirect();

        $this->assertDatabaseHas('parroquias', ['id' => $parroquia]);
    }

    public function test_no_elimina_distrito_con_municipios(): void
    {
        $this->actingAs($this->admin());
        [, $municipio] = $this->ubicacion();
        $distrito = DB::table('distritos')->insertGetId(['nombre' => 'Test Distrito', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('municipios')->where('id', $municipio)->update(['distrito_id' => $distrito]);

        $this->delete(route('distritos.destroy', $distrito))->assertRedirect();

        $this->assertDatabaseHas('distritos', ['id' => $distrito]);
        $this->assertDatabaseHas('municipios', ['id' => $municipio, 'distrito_id' => $distrito]);
    }

    public function test_elimina_parroquia_sin_dependencias(): void
    {
        $this->actingAs($this->admin());
        [, , $parroquia] = $this->ubicacion();

        $this->delete(route('parroquias.destroy', $parroquia))->assertRedirect();

        $this->assertDatabaseMissing('parroquias', ['id' => $parroquia]);
    }
}
