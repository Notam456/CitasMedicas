<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Expediente;
use App\Models\HistoricoNumero;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LiberarHistoriaTest extends TestCase
{
    use RefreshDatabase;

    private ?int $parroquiaId = null;

    private function ubicacionId(): int
    {
        if ($this->parroquiaId !== null) {
            return $this->parroquiaId;
        }

        $estadoId = DB::table('estados')->insertGetId(['nombre' => 'Test Estado', 'created_at' => now(), 'updated_at' => now()]);
        $distritoId = DB::table('distritos')->insertGetId(['nombre' => 'Test Distrito', 'created_at' => now(), 'updated_at' => now()]);
        $municipioId = DB::table('municipios')->insertGetId([
            'estado_id' => $estadoId,
            'distrito_id' => $distritoId,
            'nombre' => 'Test Municipio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->parroquiaId = DB::table('parroquias')->insertGetId([
            'municipio_id' => $municipioId,
            'nombre' => 'Test Parroquia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->parroquiaId;
    }

    private function admin(): User
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $role = Role::firstOrCreate(['name' => 'administrador']);

        $permissions = ['Dashboard', 'Usuario', 'Medico', 'Especialidad', 'Paciente', 'Procedencia', 'Planificación', 'Cita', 'Reportes', 'Patologia', 'Atender Cita', 'Reporte Cita', 'Médicos inactivos', 'Auditoría', 'Liberar Historia'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role->givePermissionTo(Permission::all());
        $admin->assignRole($role);

        return $admin;
    }

    private function crearPaciente(string $cedula, string $numero): Paciente
    {
        $paciente = Paciente::create([
            'nombre' => 'Ana',
            'apellido' => 'Perez',
            'cedula' => $cedula,
            'rif' => '',
            'fecha_nacimiento' => '1980-01-01',
            'telefono' => '04141234567',
            'parroquia_id' => $this->ubicacionId(),
            'direccion' => 'Direccion',
            'sexo' => 'Femenino',
        ]);

        Expediente::create([
            'paciente_id' => $paciente->id,
            'numero_expediente' => $numero,
            'fecha_apertura' => now()->toDateString(),
        ]);

        return $paciente;
    }

    private function crearEspecialidad(): int
    {
        return DB::table('especialidades')->insertGetId([
            'nombre' => 'Especialidad ' . uniqid(),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearCitaAgendada(Paciente $paciente, int $userId): void
    {
        $especialidadId = $this->crearEspecialidad();
        $calendarioId = DB::table('calendarios')->insertGetId([
            'especialidad_id' => $especialidadId,
            'fecha' => now()->addDay()->toDateString(),
            'hora_inicio' => '08:00',
            'hora_fin' => '12:00',
            'cupos_sucesivos' => 10,
            'cupos_primera_vez' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Cita::create([
            'paciente_id' => $paciente->id,
            'calendario_id' => $calendarioId,
            'user_id' => $userId,
            'fecha_registro' => now()->toDateString(),
            'fecha_cita' => now()->addDay()->toDateString(),
            'estado' => 'Agendada',
            'tipo_paciente' => 'primera_vez',
            'historia_traida' => false,
        ]);
    }

    public function test_libera_numero_y_marca_paciente_inactivo(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');

        $response = $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $response->assertRedirect(route('paciente.index'));

        $this->assertDatabaseHas('historico_numeros', [
            'paciente_id' => $paciente->id,
            'numero_expediente' => '11-11-11',
            'motivo' => 'sin_retorno',
            'vigente' => false,
        ]);

        $this->assertNull($paciente->fresh()->expediente->numero_expediente);
        $this->assertEquals('inactivo', $paciente->fresh()->estado);
        $this->assertEquals('sin_retorno', $paciente->fresh()->estado_motivo);
        $this->assertNotNull($paciente->fresh()->fecha_baja);
    }

    public function test_no_libera_si_tiene_citas_agendadas(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');

        $this->crearCitaAgendada($paciente, $admin->id);

        $response = $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $response->assertRedirect(route('paciente.index'));

        $this->assertDatabaseMissing('historico_numeros', [
            'paciente_id' => $paciente->id,
            'vigente' => false,
        ]);
        $this->assertNotNull($paciente->fresh()->expediente->numero_expediente);
        $this->assertNotEquals('inactivo', $paciente->fresh()->estado);
    }

    public function test_liberar_actualiza_fila_vigente_sin_dejar_fantasma(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');
        HistoricoNumero::asignar($paciente, '11-11-11');

        $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $this->assertDatabaseHas('historico_numeros', [
            'paciente_id' => $paciente->id,
            'numero_expediente' => '11-11-11',
            'motivo' => 'sin_retorno',
            'vigente' => false,
        ]);

        $this->assertDatabaseMissing('historico_numeros', ['paciente_id' => $paciente->id, 'vigente' => true]);
        $this->assertEquals(1, HistoricoNumero::where('paciente_id', $paciente->id)->where('numero_expediente', '11-11-11')->count());
    }

    public function test_no_libera_paciente_inactivo(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');
        $paciente->update(['estado' => 'inactivo', 'estado_motivo' => 'sin_retorno', 'fecha_baja' => now()->toDateString()]);
        $paciente->expediente->update(['numero_expediente' => null]);

        $response = $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $response->assertRedirect(route('paciente.index'));
        $this->assertDatabaseMissing('historico_numeros', ['paciente_id' => $paciente->id]);
    }

    public function test_no_libera_sin_motivo_validado(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');

        $response = $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'invalido']);

        $response->assertSessionHasErrors('motivo');
        $this->assertDatabaseMissing('historico_numeros', ['paciente_id' => $paciente->id]);
    }

    public function test_requiere_permiso_liberar_historia(): void
    {
        $usuario = User::create([
            'name' => 'SinPermiso',
            'email' => 'sinpermiso@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($usuario);

        $paciente = $this->crearPaciente('X-11111111', '11-11-11');

        $response = $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertDatabaseMissing('historico_numeros', ['paciente_id' => $paciente->id]);
    }

    public function test_paciente_liberado_aparece_en_busqueda_por_numero(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('X-11111111', '11-11-11');

        $this->post(route('pacientes.liberar-historia', $paciente), ['motivo' => 'sin_retorno']);

        $response = $this->get(route('paciente.buscar') . '?q=11-11-11');
        $data = $response->json();

        $this->assertArrayHasKey('liberado', $data);
        $this->assertTrue($data['liberado']);
        $this->assertStringContainsString('Ana', $data['paciente']);
        $this->assertEquals('sin_retorno', $data['motivo']);
    }

    public function test_numero_liberado_puede_reutilizarse_por_otro_paciente(): void
    {
        $this->actingAs($this->admin());
        $a = $this->crearPaciente('X-11111111', '11-11-11');
        $this->post(route('pacientes.liberar-historia', $a), ['motivo' => 'sin_retorno']);

        $b = $this->crearPaciente('X-22222222', '11-11-11');
        HistoricoNumero::asignar($b, '11-11-11');

        $this->assertEquals('11-11-11', $b->fresh()->expediente->numero_expediente);
        $this->assertNull($a->fresh()->expediente->numero_expediente);

        // Sin filas fantasma: solo una vigente al reutilizar (de B), ninguna para A
        $this->assertEquals(1, HistoricoNumero::where('numero_expediente', '11-11-11')->where('vigente', true)->count());
        $this->assertDatabaseMissing('historico_numeros', ['paciente_id' => $a->id, 'vigente' => true]);
        $this->assertEquals(1, HistoricoNumero::where('paciente_id', $b->id)->where('vigente', true)->count());
    }

    public function test_paciente_que_vuelve_recibe_numero_nuevo_y_reactiva(): void
    {
        $this->actingAs($this->admin());
        $a = $this->crearPaciente('X-11111111', '11-11-11');
        $this->post(route('pacientes.liberar-historia', $a), ['motivo' => 'sin_retorno']);

        $b = $this->crearPaciente('X-22222222', '11-11-11');
        HistoricoNumero::asignar($b, '11-11-11');

        // A vuelve y se le asigna un número nuevo (flujo real de reasignación)
        $a->expediente->update(['numero_expediente' => '12-12-12']);
        HistoricoNumero::asignar($a, '12-12-12');
        $a->update(['estado' => 'activo', 'estado_motivo' => null, 'fecha_baja' => null]);

        // A reactivado, con 2 números en su histórico (el liberado y el nuevo)
        $this->assertEquals('activo', $a->fresh()->estado);
        $this->assertEquals('12-12-12', $a->fresh()->expediente->numero_expediente);
        $this->assertEquals(2, $a->fresh()->historicoNumeros()->count());

        // Exactamente una vigente por paciente (sin fantasma ni duplicados)
        $this->assertEquals(1, HistoricoNumero::where('paciente_id', $a->id)->where('vigente', true)->count());
        $this->assertEquals(1, HistoricoNumero::where('paciente_id', $b->id)->where('vigente', true)->count());

        // Sin colisión: B conserva 11-11-11, A tiene 12-12-12
        $this->assertEquals('11-11-11', $b->fresh()->expediente->numero_expediente);
        $this->assertNotEquals($a->fresh()->expediente->numero_expediente, $b->fresh()->expediente->numero_expediente);
    }
}
