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

class AgendarCitaTest extends TestCase
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

    private function crearEspecialidad(): int
    {
        return DB::table('especialidades')->insertGetId([
            'nombre' => 'Especialidad ' . uniqid(),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearPaciente(string $cedula, ?string $numero = null): Paciente
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

        if ($numero !== null) {
            Expediente::create([
                'paciente_id' => $paciente->id,
                'numero_expediente' => $numero,
                'fecha_apertura' => now()->toDateString(),
            ]);
        }

        return $paciente;
    }

    /**
     * Datos válidos para agendar una cita tipo "control" sin médico fijo.
     */
    private function payload(array $overrides = []): array
    {
        $this->ubicacionId();
        $especialidadId = $this->crearEspecialidad();
        $fecha = now()->addDay()->toDateString();
        $calendarioId = DB::table('calendarios')->insertGetId([
            'medico_id' => null,
            'especialidad_id' => $especialidadId,
            'fecha' => $fecha,
            'hora_inicio' => '08:00',
            'hora_fin' => '12:00',
            'cupos_sucesivos' => 10,
            'cupos_primera_vez' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estadoId = DB::table('estados')->first()->id;
        $municipioId = DB::table('municipios')->where('estado_id', $estadoId)->first()->id;

        return array_merge([
            'cedula_tipo' => 'V',
            'cedula' => '123456789',
            'numero_expediente' => '',
            'rif' => '',
            'nombre' => 'Maria Garcia',
            'apellido' => 'Lopez',
            'fecha_nacimiento' => '1990-05-10',
            'telefono' => '04141234567',
            'estado_id' => $estadoId,
            'municipio_id' => $municipioId,
            'parroquia_id' => $this->parroquiaId,
            'direccion' => '',
            'sexo' => 'Femenino',
            'calendario_id' => $calendarioId,
            'fecha_cita' => $fecha,
            'observacion' => '',
            'especialidad_id' => $especialidadId,
            'medico_id' => '',
            'tipo_paciente' => 'control',
        ], $overrides);
    }

    // ===== Búsqueda de paciente y desambiguación cédula / N° de historia =====

    public function test_buscar_cedula_no_registrada_devuelve_tipo_cedula_y_valor(): void
    {
        $this->actingAs($this->admin());

        $response = $this->getJson(route('paciente.buscar') . '?q=12345678');

        $response->assertOk()
            ->assertJson(['encontrado' => false, 'tipo' => 'cedula', 'valor' => '12345678']);
    }

    public function test_buscar_normaliza_cedula_con_prefijo_sin_guiones(): void
    {
        $this->actingAs($this->admin());
        $this->crearPaciente('V-12345678');

        // El usuario escribe "V12345678" sin guión y el paciente debe aparecer.
        $response = $this->getJson(route('paciente.buscar') . '?q=V12345678');

        $response->assertOk()
            ->assertJson(['encontrado' => true])
            ->assertJsonPath('datos.cedula', 'V-12345678');
    }

    public function test_buscar_por_seis_digitos_infiere_numero_de_historia(): void
    {
        $this->actingAs($this->admin());

        $response = $this->getJson(route('paciente.buscar') . '?q=123456');

        $response->assertOk()
            ->assertJson(['encontrado' => false, 'tipo' => 'historia', 'valor' => '12-34-56']);
    }

    public function test_buscar_por_seis_digitos_sin_guiones_encuentra_por_historia(): void
    {
        $this->actingAs($this->admin());
        $this->crearPaciente('V-55556666', '12-34-56');

        $response = $this->getJson(route('paciente.buscar') . '?q=123456');

        $response->assertOk()
            ->assertJson(['encontrado' => true])
            ->assertJsonPath('datos.expediente.numero_expediente', '12-34-56');
    }

    public function test_buscar_por_numero_corto_informa_incompleto(): void
    {
        $this->actingAs($this->admin());

        $response = $this->getJson(route('paciente.buscar') . '?q=1234');

        $response->assertOk()
            ->assertJson(['encontrado' => false, 'tipo' => 'incompleto']);
    }

    // ===== Agenda de cita =====

    public function test_agenda_cita_sin_medico_fijo_cualquier_medico(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('Citas.store'), $this->payload());

        $response->assertRedirect(route('Citas.create'));

        $this->assertDatabaseHas('citas', [
            'tipo_paciente' => 'control',
            'estado' => 'Agendada',
        ]);
        $this->assertDatabaseHas('pacientes', ['cedula' => 'V-123456789']);
    }

    public function test_agenda_cita_con_numero_de_historia_nuevo_registra_asignacion(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('Citas.store'), $this->payload(['numero_expediente' => '22-22-22']));

        $this->assertDatabaseHas('historico_numeros', [
            'numero_expediente' => '22-22-22',
            'vigente' => true,
        ]);
        $this->assertEquals(1, HistoricoNumero::where('numero_expediente', '22-22-22')->count());
    }

    public function test_agendar_cita_no_duplica_historico_si_numero_ya_asignado(): void
    {
        $this->actingAs($this->admin());
        $paciente = $this->crearPaciente('V-123456789', '12-34-56');
        HistoricoNumero::asignar($paciente, '12-34-56');

        $this->assertEquals(1, HistoricoNumero::where('numero_expediente', '12-34-56')->count());

        $this->post(route('Citas.store'), $this->payload());

        // El número no cambió: no debe crearse una fila "liberada" fantasma por cada cita.
        $this->assertEquals(1, HistoricoNumero::where('numero_expediente', '12-34-56')->count());
        $this->assertDatabaseMissing('historico_numeros', ['numero_expediente' => '12-34-56', 'vigente' => false]);
    }

    public function test_rechaza_numero_de_historia_que_pertenece_a_otro_paciente(): void
    {
        $this->actingAs($this->admin());
        $this->crearPaciente('V-11111111', '33-33-33');

        $response = $this->post(route('Citas.store'), $this->payload(['numero_expediente' => '33-33-33']));

        $response->assertSessionHasErrors(['numero_expediente' => 'Número de Historia en uso']);
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_requiere_estado_y_municipio(): void
    {
        $this->actingAs($this->admin());
        $payload = $this->payload();
        unset($payload['estado_id'], $payload['municipio_id']);

        $response = $this->post(route('Citas.store'), $payload);

        $response->assertSessionHasErrors(['estado_id', 'municipio_id']);
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_telefono_mayor_a_15_caracteres_se_rechaza(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('Citas.store'), $this->payload(['telefono' => '04141234567890123']));

        $response->assertSessionHasErrors('telefono');
        $this->assertDatabaseCount('citas', 0);
    }
}