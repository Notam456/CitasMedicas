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
use App\Http\Controllers\SuspensionMedicoController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\AuditoriaController;

use function PHPUnit\Framework\returnValue;
use App\Http\Controllers\NotificacionController;

//Ruta de inicio
Route::get('/', function () {
    return view('login');
});

//Rutas para las vistas de autenticación
Route::view('/login', 'login')->name('login');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::middleware('throttle:login')->group(function () {
    Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar-sesion');
});
Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');

//Ruta user
Route::middleware(['auth', 'can:Usuario'])->group(function () {
    Route::resource('users', UserController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('users', UserController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('throttle:crud_escritura');
    Route::put('/roles/{role:name}', [RoleController::class, 'update'])->name('roles.update')->middleware('throttle:crud_escritura');
    Route::get('/roles/{role:name}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions')->middleware('throttle:crud_lectura');
});

//Ruta maestros
Route::middleware(['auth', 'can:Paciente'])->group(function () {
    Route::resource('paciente', PacienteController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('paciente', PacienteController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
});

Route::middleware(['auth', 'can:Especialidad'])->group(function () {
    Route::resource('especialidades', EspecialidadController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('especialidades', EspecialidadController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
});

Route::middleware(['auth', 'can:Medico'])->group(function () {
    Route::resource('medicos', MedicoController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('medicos', MedicoController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
});

Route::middleware(['auth', 'can:Procedencia'])->group(function () {
    Route::resource('estados', EstadoController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('estados', EstadoController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
    Route::get('/api/estados', [EstadoController::class, 'getEstados'])->middleware('throttle:crud_lectura');

    Route::resource('municipios', MunicipioController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('municipios', MunicipioController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');

    Route::resource('parroquias', ParroquiaController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('parroquias', ParroquiaController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');

    Route::resource('distritos', DistritoController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('distritos', DistritoController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
    Route::get('/api/distritos', [DistritoController::class, 'getDistritosData'])->name('api.distritos')->middleware('throttle:crud_lectura');
    Route::get('/api/municipios-disponibles/{distrito_id?}', [MunicipioController::class, 'getDisponibles'])->name('api.municipios-disponibles')->middleware('throttle:crud_lectura');
});

//Rutas para Agendar Cita

// Rutas de API

Route::middleware(['auth', 'can:Cita,Pacientes'])->group(function () {
    Route::get('/api/municipios/{estado_id}', [MunicipioController::class, 'getMunicipios'])->middleware('throttle:citas_flujo');
    Route::get('/api/parroquias/{municipio_id}', [ParroquiaController::class, 'getParroquias'])->middleware('throttle:citas_flujo');
});

Route::middleware(['auth', 'can:Cita'])->group(function () {
    Route::get('api/paciente/buscar', [PacienteController::class, 'buscarPaciente'])->name('paciente.buscar')->middleware('auth')->middleware('throttle:citas_flujo');
    Route::get('/api/especialidades/{id}/medicos', [CitaController::class, 'getMedicosPorEspecialidad'])->middleware('throttle:citas_flujo');
    Route::get('/api/medicos/{medico_id}/disponibilidad', [CitaController::class, 'disponibilidadMes'])->middleware('throttle:citas_flujo');
    Route::get('/api/citas/paciente/{paciente_id}/especialidad/{especialidad_id}/tiene-citas', [CitaController::class, 'tieneCitasEnEspecialidad'])->middleware('throttle:citas_flujo');
    Route::get('/api/medicos/{medico_id}/suspensiones-activas', [SuspensionMedicoController::class, 'getActiveSuspensions'])->name('api.medicos.suspensiones-activas')->middleware('throttle:citas_flujo');

    //Rutas resource
    Route::resource('Citas', CitaController::class)->parameters(['Citas' => 'cita'])->only(['index', 'create', 'show'])->middleware('throttle:citas_flujo');
    Route::resource('Citas', CitaController::class)->parameters(['Citas' => 'cita'])->only(['store'])->middleware('throttle:crud_escritura');
    Route::get('/Citas/{id}/show', [CitaController::class, 'show'])->middleware('throttle:citas_flujo');
});

Route::get('/calendario/medicos/{especialidad}', [CalendarioController::class, 'getMedicos'])->middleware('throttle:crud_lectura');
Route::get('/calendario/eventos', [CalendarioController::class, 'getDatosMes'])->middleware('throttle:crud_lectura');
Route::resource('calendario', CalendarioController::class)->only(['index', 'create', 'show', 'edit'])->middleware(['auth', 'can:Planificación', 'throttle:crud_lectura']);
Route::resource('calendario', CalendarioController::class)->only(['store', 'update', 'destroy'])->middleware(['auth', 'can:Planificación', 'throttle:crud_escritura']);



Route::get('/municipios-por-estado/{estado_id}', [ParroquiaController::class, 'getMunicipiosPorEstado'])->middleware('throttle:crud_lectura');

// Notificaciones
Route::middleware('auth')->group(function () {
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index')->middleware('throttle:crud_lectura');
    Route::get('/notificaciones/no-leidas', [NotificacionController::class, 'unread'])->name('notificaciones.unread')->middleware('throttle:crud_lectura');
    Route::put('/notificaciones/{id}/leida', [NotificacionController::class, 'markAsRead'])->name('notificaciones.markAsRead')->middleware('throttle:crud_escritura');
    Route::put('/notificaciones/leer-todas', [NotificacionController::class, 'markAllAsRead'])->name('notificaciones.markAllAsRead')->middleware('throttle:crud_escritura');
    Route::delete('/notificaciones/{id}', [NotificacionController::class, 'destroy'])->name('notificaciones.destroy')->middleware('throttle:crud_escritura');
});

// Expedientes (N° Historia)
Route::middleware('auth')->group(function () {
    Route::post('/citas/{cita}/historia-traida', [MorbilidadController::class, 'toggleHistoriaTraida'])->name('citas.historia-traida')->middleware('throttle:crud_escritura');
    Route::post('/pacientes/{paciente}/expediente', [ExpedienteController::class, 'guardar'])->name('expedientes.guardar')->middleware('throttle:crud_escritura');
});


// Dashboard y Reportes Yajure

Route::middleware(['auth', 'can:Atender Cita'])->group(function () {
    Route::get('/morbilidad/pendientes', [MorbilidadController::class, 'pendientes'])->name('morbilidad.pendientes')->middleware('throttle:atender_citas');
    Route::get('/citas/{cita}/atender', [DiagnosticoController::class, 'atender'])->name('citas.atender')->middleware('throttle:detalle_cita');
    Route::post('/citas/{cita}/diagnostico', [DiagnosticoController::class, 'store'])->name('citas.diagnostico.store')->middleware('throttle:crud_escritura');
    Route::get('/diagnosticos/{diagnostico}/edit', [DiagnosticoController::class, 'edit'])->name('diagnosticos.edit')->middleware('throttle:detalle_cita');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar')->middleware('throttle:crud_escritura');
});
Route::middleware(['auth', 'can:Reporte Cita'])->group(function () {
    Route::get('/morbilidad', [MorbilidadController::class, 'index'])->name('morbilidad.index')->middleware('throttle:gestion_citas');

    Route::get('/morbilidad/{cita}', [MorbilidadController::class, 'getCita'])->name('morbilidad.getCita')->middleware('throttle:detalle_cita');
    Route::get('/morbilidad/{cita}/pdf', [MorbilidadController::class, 'pdfCita'])->name('morbilidad.pdfCita')->middleware('throttle:exportaciones');
});

Route::get('/api/patologias/por-cita/{cita}', function ($citaId) {
    $cita = App\Models\Cita::findOrFail($citaId);
    $especialidadId = $cita->medico->especialidad_id;
    return App\Models\Patologia::where('especialidad_id', $especialidadId)->get();
})->middleware('auth')->middleware('throttle:detalle_cita');

Route::middleware(['auth', 'can:Patologia'])->group(function () {
    Route::resource('patologias', PatologiaController::class)->only(['index', 'create', 'show', 'edit'])->middleware('throttle:crud_lectura');
    Route::resource('patologias', PatologiaController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:crud_escritura');
});

Route::middleware(['auth', 'can:Reportes'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index')->middleware('throttle:reportes_index');
    Route::get('/reportes/pdf/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/medicos-por-especialidad/excel', [ReporteController::class, 'exportarMedicosPorEspecialidadExcel'])->name('reportes.medicos_especialidad_excel')->middleware('throttle:exportaciones');

    Route::get('/reportes/pdf/procedencia-pacientes', [ReporteController::class, 'procedenciaPacientes'])->name('reportes.procedencia_pacientes_pdf')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/procedencia-pacientes/excel', [ReporteController::class, 'exportarProcedenciaExcel'])->name('reportes.procedencia_pacientes_excel')->middleware('throttle:exportaciones');

    Route::get('/reportes/pdf/movimiento-consultas/pdf', [ReporteController::class, 'movimientoConsultasPdf'])->name('reportes.movimiento_consultas_pdf')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/movimiento-consultas/excel', [ReporteController::class, 'movimientoConsultasExcel'])->name('reportes.movimiento_consultas_excel')->middleware('throttle:exportaciones');

    Route::get('/reportes/pdf/movimiento-consulta-aro/pdf', [ReporteController::class, 'movimientoConsultaAroPdf'])->name('reportes.movimiento_consulta_aro_pdf')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/movimiento-consulta-aro/excel', [ReporteController::class, 'movimientoConsultaAroExcel'])->name('reportes.movimiento_consulta_aro_excel')->middleware('throttle:exportaciones');

    Route::get('/reportes/pdf/causas-principales/pdf', [ReporteController::class, 'causasPrincipalesPdf'])->name('reportes.causas_principales_pdf')->middleware('throttle:exportaciones');
    Route::get('/reportes/excel/causas-principales/excel', [ReporteController::class, 'causasPrincipalesExcel'])->name('reportes.causas_principales_excel')->middleware('throttle:exportaciones');
});

Route::middleware(['auth', 'can:Médicos inactivos'])->group(function () {
    Route::get('/suspensiones', [SuspensionMedicoController::class, 'index'])->name('suspensiones.index')->middleware('throttle:crud_lectura');
    Route::post('/suspensiones', [SuspensionMedicoController::class, 'store'])->name('suspensiones.store')->middleware('throttle:crud_escritura');
    Route::delete('/suspensiones/{id}', [SuspensionMedicoController::class, 'destroy'])->name('suspensiones.destroy')->middleware('throttle:crud_escritura');
    Route::get('/api/medicos/{medico_id}/suplentes-disponibles', [SuspensionMedicoController::class, 'getSuplentesDisponibles'])->name('api.medicos.suplentes-disponibles')->middleware('throttle:crud_lectura');
    Route::get('/api/medicos/{medico_id}/citas-activas-count', [SuspensionMedicoController::class, 'getCitasActivasCount'])->name('api.medicos.citas-activas-count')->middleware('throttle:crud_lectura');
});

Route::middleware(['auth', 'can:Auditoría'])->group(function () {
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index')->middleware('throttle:crud_lectura');
    Route::get('/auditoria/{id}', [AuditoriaController::class, 'show'])->name('auditoria.show')->middleware('throttle:crud_lectura');
});

