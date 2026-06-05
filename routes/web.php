<?php

use App\Http\Controllers\CalendarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MorbilidadController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\PatologiaController;

use function PHPUnit\Framework\returnValue;

//Ruta de inicio
Route::get('/', function () {return view('login');}); 

//Rutas para las vistas de autenticación
Route::view('/login', 'login')->name('login');
Route::view('/signup', 'signup')->name('signup');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar-sesion');
Route::post('/validar-registro', [LoginController::class, 'register'])->name('register');
Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');

//Ruta user
Route::middleware(['auth', 'can:Usuarios'])->group(function () {
    Route::resource('users', UserController::class);
});

//Ruta maestros
Route::middleware(['auth', 'can:Paciente'])->group(function () {
    Route::resource('paciente', PacienteController::class);
    Route::get('/paciente/{id}/edit', [PacienteController::class, 'edit']);
    Route::get('/paciente/{id}/show', [PacienteController::class, 'show']);
});

Route::middleware(['auth', 'can:Especialidad'])->group(function () {
    Route::resource('especialidades', EspecialidadController::class);
    Route::get('/especialidades/{id}/edit', [EspecialidadController::class, 'edit']);
    Route::get('/especialidades/{id}/show', [EspecialidadController::class, 'show']);
});

Route::middleware(['auth', 'can:Médicos'])->group(function () {
    Route::resource('medicos', MedicoController::class);
    Route::get('/medicos/{id}/edit', [MedicoController::class, 'edit']);
    Route::get('/medicos/{id}/show', [MedicoController::class, 'show']);
});

Route::middleware(['auth', 'can:Procedencia'])->group(function () {
    Route::resource('estados', EstadoController::class);
    Route::get('/api/estados', [EstadoController::class, 'getEstados']);
    Route::get('/estados/{id}/edit', [EstadoController::class, 'edit']);
    Route::get('/estados/{id}/show', [EstadoController::class, 'show']);

    Route::resource('municipios', MunicipioController::class);
    Route::get('/municipios/{id}/edit', [MunicipioController::class, 'edit']);
    Route::get('/municipios/{id}/show', [MunicipioController::class, 'show']);

    Route::resource('parroquias', ParroquiaController::class);
    Route::get('/parroquias/{id}/edit', [ParroquiaController::class, 'edit']);
    Route::get('/parroquias/{id}/show', [ParroquiaController::class, 'show']);
    
    Route::resource('distritos', DistritoController::class);
    Route::get('/api/distritos', [DistritoController::class, 'getDistritosData'])->name('api.distritos');
});

//Rutas para Agendar Cita

// Rutas de API
Route::get('api/paciente/buscar/{cedula}', [PacienteController::class, 'buscarPorCedula'])->name('paciente.buscar')->middleware('auth');
Route::get('/api/especialidades/{id}/medicos', [CitaController::class, 'getMedicosPorEspecialidad']);
Route::get('/api/medicos/{medico_id}/disponibilidad', [CitaController::class, 'disponibilidadMes']);
Route::get('/api/municipios/{estado_id}', function($estado_id) {return App\Models\Municipio::where('estado_id', $estado_id)->get();});
Route::get('/api/parroquias/{municipio_id}', function($municipio_id) {return App\Models\Parroquia::where('municipio_id', $municipio_id)->get();});
//Rutas resource
Route::resource('Citas', CitaController::class)->parameters(['Citas' => 'cita'])->middleware(['auth', 'can:Citas']);
Route::get('/Citas/{id}/show', [CitaController::class, 'show'])->middleware(['auth', 'can:Citas']);


Route::get('/calendario/medicos/{especialidad}', [CalendarioController::class, 'getMedicos']);
Route::get('/calendario/eventos', [CalendarioController::class, 'getDatosMes']);
Route::resource('calendario', CalendarioController::class)->middleware(['auth', 'can:Planificación']);



Route::get('/municipios-por-estado/{estado_id}', [ParroquiaController::class, 'getMunicipiosPorEstado']);

// Dashboard y Reportes Yajure

// Route::get('/', [DashboardController::class, 'index'])->name('inicio'); // yajure: no me borren este bypass por favor!

Route::get('/api/medicamentos', function() {
    return App\Models\Medicamento::all();
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/citas/{cita}/atender', [DiagnosticoController::class, 'atender'])->name('citas.atender');
    Route::post('/citas/{cita}/diagnostico', [DiagnosticoController::class, 'store'])->name('citas.diagnostico.store');
});

Route::middleware(['auth', 'can:Morbilidad'])->group(function () {
    Route::get('/morbilidad', [MorbilidadController::class, 'index'])->name('morbilidad.index');
    Route::get('/morbilidad/pendientes', [MorbilidadController::class, 'pendientes'])->name('morbilidad.pendientes');

    Route::get('/diagnosticos', [DiagnosticoController::class, 'index'])->name('diagnosticos.index');
    Route::get('/diagnosticos/{diagnostico}/edit', [DiagnosticoController::class, 'edit'])->name('diagnosticos.edit');
    Route::put('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'update'])->name('diagnosticos.update');
    Route::delete('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'destroy'])->name('diagnosticos.destroy');
    Route::get('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'show'])->name('diagnosticos.show');
});

Route::get('/api/patologias/por-cita/{cita}', function($citaId) {
    $cita = App\Models\Cita::findOrFail($citaId);
    $especialidadId = $cita->medico->especialidad_id;
    return App\Models\Patologia::where('especialidad_id', $especialidadId)->get();
})->middleware('auth');

Route::middleware(['auth', 'can:Patologia'])->group(function () {
    Route::resource('patologias', PatologiaController::class);
});

Route::middleware(['auth', 'can:Reportes'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/pdf/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad');
    Route::get('/reportes/excel/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel');
    Route::get('/reportes/excel/medicos-por-especialidad/excel', [ReporteController::class, 'exportarMedicosPorEspecialidadExcel'])->name('reportes.medicos_especialidad_excel');

    Route::get('/reportes/pdf/procedencia-pacientes', [ReporteController::class, 'procedenciaPacientes'])->name('reportes.procedencia_pacientes_pdf');
    Route::get('/reportes/excel/procedencia-pacientes/excel', [ReporteController::class, 'exportarProcedenciaExcel'])->name('reportes.procedencia_pacientes_excel');

    Route::get('/reportes/pdf/movimiento-consultas/pdf', [ReporteController::class, 'movimientoConsultasPdf'])->name('reportes.movimiento_consultas_pdf');
    Route::get('/reportes/excel/movimiento-consultas/excel', [ReporteController::class, 'movimientoConsultasExcel'])->name('reportes.movimiento_consultas_excel');

    Route::get('/reportes/pdf/causas-principales/pdf', [ReporteController::class, 'causasPrincipalesPdf'])->name('reportes.causas_principales_pdf');
    Route::get('/reportes/excel/causas-principales/excel', [ReporteController::class, 'causasPrincipalesExcel'])->name('reportes.causas_principales_excel');
});

Route::middleware(['auth', 'can:Usuarios'])->group(function () {
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role:name}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('/roles/{role:name}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
});

