# AGENTS.md — SAGECIM (Sistema de Gestión de Citas Médicas)

Este archivo brinda contexto técnico completo para cualquier agente de IA que trabaje sobre este proyecto. Lee todo antes de hacer cambios.

---

## 1. Visión general del proyecto

**SAGECIM** es una aplicación web para la administración de consultas médicas y citas de un centro de salud. Permite:

- Gestionar el catálogo de especialidades, médicos, pacientes, patologías y procedencia (estados, municipios, parroquias, distritos).
- Programar citas con control de disponibilidad y cupos.
- Registrar atención y diagnósticos de cada paciente (morbilidad).
- Suspender médicos con asignación de suplentes.
- Generar reportes operativos y gerenciales con exportación a **PDF** y **Excel**.
- Lanzador de escritorio (`.vbs` + PowerShell) para arrancar el sistema con un doble clic en equipos locales.

**Autores:** José Yajure, Daniel Alejos, Roberto Vielma, Yhoenyer Alvarado, Jose Gonzalez.

---

## 2. Stack tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| Backend | PHP + Laravel | ^8.2 / 12.x |
| Base de datos | PostgreSQL | 17 |
| Frontend | Blade + Tailwind CSS + Vite | 4 |
| Autenticación / RBAC | spatie/laravel-permission | ^7.4 |
| Auditoría | owen-it/laravel-auditing | ^14.0 |
| Exportación Excel | maatwebsite/excel | ^3.1 |
| Exportación PDF | carlos-meneses/laravel-mpdf | * |
| Alertas | realrashid/sweet-alert | ^7.3 |
| Pruebas | PHPUnit | ^11.5 |

---

## 3. Estructura de directorios clave

```
app/
  Enums/              CitaEstado, CitaCancelacionMotivo, CitaTipoPaciente, Sexo
  Exports/            Exportaciones a Excel (maatwebsite)
  Models/             Modelos Eloquent (ver sección 6)
  Notifications/      Notificaciones internas (CitaCancelada, NuevoPaciente, etc.)
  Services/           CitaService, MorbilidadService, ReporteService, DataTableService
  Support/            Membrete (para reportes PDF)
  Http/Controllers/   Controladores por módulo (ver sección 5)
database/
  migrations/         Migraciones ordenadas por fecha
  seeders/            Seeders (UserSeeder, PermissionSeeder, EspecialidadSeeder, etc.)
  factories/          (vacías — solo User usa HasFactory)
routes/
  web.php             Todas las rutas web (sin archivo api.php separado)
config/
  citas.php           Config custom: especialidades exclusivas para sexo femenino
launcher/             Lanzador de escritorio (.vbs + PowerShell + config.json)
tests/                Pruebas automatizadas (limitadas)
```

---

## 4. Instalación y puesta en marcha

```bash
# 1. Clonar e instalar dependencias PHP
composer install

# 2. Copiar .env y configurar PostgreSQL
cp .env.example .env
# Editar .env: DB_CONNECTION=pgsql, DB_HOST, DB_PORT=5432, DB_DATABASE=hospital, etc.

# 3. Generar clave de la app
php artisan key:generate

# 4. Migrar y sembrar (SIEMPRE usar migrate:fresh al sembrar)
php artisan migrate:fresh --seed

# 5. Compilar assets frontend
npm install
npm run build

# 6. Servidor
php artisan serve
# http://127.0.0.1:8000

# 7. Tests
composer test
```

> **Importante:** `.env` está en `.gitignore`. Nunca commitear credenciales.
> Usar siempre `migrate:fresh --seed` (no solo `migrate`) al sembrar, para evitar errores de clave duplicada en índicios únicos case-insensitive.

---

## 5. Rutas y autorización

Todas las rutas están en `routes/web.php` (no hay `api.php`). El patrón de autorización es:

```php
Route::middleware(['auth', 'can:Permiso'])->group(function () { ... });
```

Los middleware de throttling se aplican por grupo de operación:

| Middleware | Uso |
|------------|-----|
| `throttle:crud_lectura` | Lectura de catálogos |
| `throttle:crud_escritura` | Escritura de catálogos |
| `throttle:citas_flujo` | API de citas (búsqueda, disponibilidad) |
| `throttle:atender_citas` | Atención de citas / morbilidad |
| `throttle:detalle_cita` | Detalle de cita individual |
| `throttle:exportaciones` | Exportaciones PDF/Excel |
| `throttle:gestion_citas` | Gestión de citas |
| `throttle:reportes_index` | Índice de reportes |
| `throttle:login` | Login |

### Módulos y sus permisos (Spatie)

| Módulo | Rutas base | Permisos típicos |
|--------|-----------|------------------|
| Usuarios / Roles | `/users`, `/roles` | `Usuario` |
| Pacientes | `/paciente` | `Paciente` |
| Especialidades | `/especialidades` | `Especialidad` |
| Médicos | `/medicos` | `Medico` |
| Procedencia | `/estados`, `/municipios`, `/parroquias`, `/distritos` | `Procedencia` |
| Citas | `/Citas` | `Cita` |
| Calendario | `/calendario` | `Planificación` |
| Morbilidad / Diagnóstico | `/morbilidad`, `/citas/{cita}/atender` | `Atender Cita`, `Editar atencion`, `Gestión de Cita` |
| Patologías | `/patologias` | `Patologia` |
| Reportes | `/reportes` | `Reportes` |
| Suspensiones | `/suspensiones` | `Médicos inactivos` |
| Auditoría | `/auditoria` | `Auditoría` |
| Notificaciones | `/notificaciones` | (solo `auth`) |

### ⚠️ Problemas conocidos de autorización (ver `analisisProblemas-23-06-2026.md`)

1. **Ausencia total de autorización en controladores:** Ningún controlador usa `$this->authorize()` ni Policies. Solo los middleware de ruta.
2. **Rutas públicas sin `auth`:** `GET /calendario/medicos/{especialidad}`, `GET /calendario/eventos`, `GET /municipios-por-estado/{estado_id}`.
3. **Middleware `can:Cita,Pacientes` mal configurado:** La coma no crea AND/OR de permisos; Laravel la interpreta como gate + parámetro.
4. **Rutas duplicadas:** `CitaController@show` está en `Route::resource('Citas')` y en `Route::get('/Citas/{id}/show')`.
5. **Nombres de ruta inconsistentes:** Algunas rutas usan PascalCase (`/Citas`), otras minúsculas.

---

## 6. Modelos Eloquent (app/Models/)

| Modelo | Tabla | Relaciones clave | Observaciones |
|--------|-------|------------------|---------------|
| `User` | `users` | roles, permissions (Spatie) | Único con `HasFactory` y `$casts` |
| `Cita` | `citas` | user, paciente, calendario, medico (hasOneThrough), especialidad (hasOneThrough), patologias | Auditable (OwenIt). Casts: `historia_traida` => boolean |
| `Calendario` | `calendarios` | medico, especialidad, citas | Disponibilidad y cupos |
| `Medico` | `medicos` | especialidad, calendarios, suspensiones | |
| `Paciente` | `pacientes` | expediente, citas | Relación `Expediente()` viola PSR-1 (debería ser `expediente()`) |
| `Especialidad` | `especialidades` | medicos, calendarios, patologias | Algunas son solo-femenino (config/citas.php) |
| `Estado` | `estados` | municipios | |
| `Municipio` | `municipios` | estado, parroquias, distrito_id | |
| `Parroquia` | `parroquias` | municipio | |
| `Distrito` | `distritos` | municipios | |
| `Patologia` | `patologias` | especialidad | |
| `CitaPatologia` | `cita_patologia` | cita, patologia | Tabla puente |
| `SuspensionMedico` | `suspensiones_medicos` | medico, suplente | |
| `HorarioMedico` | `horario_medico` | medico | |
| `Expediente` | `expedientes` | paciente | |
| `AroCitaDato` | `aro_cita_datos` | cita | Datos especiales para embarazadas (ARO) |
| `CitaCancelacion` | `cita_cancelaciones` | cita | Motivo de cancelación |
| `HistoricoNumero` | `historicos_numeros` | | Historial de números de expediente |
| `LanzadorSesion` | `lanzador_sesiones` | | Sesiones del lanzador de escritorio |
| `Notificacion` | `notifications` | | Notificaciones internas |

### Convenciones de modelos (problemas conocidos)

- Solo `User` tiene `use HasFactory` y `$casts`. Los otros 15 modelos no.
- Algunos modelos no definen `$table` (`Paciente`, `Distrito`, `Expediente`, `Patologia`, etc.).
- Ningún archivo PHP declara `declare(strict_types=1)`.
- `Paciente::Expediente()` usa mayúscula inicial (violación PSR-1).

---

## 7. Servicios de negocio

| Servicio | Responsabilidad |
|----------|-----------------|
| `CitaService` | Lógica de creación, cancelación, reagendamiento de citas |
| `MorbilidadService` | Registro de diagnósticos, pendientes, historia traída |
| `ReporteService` | Consulta de datos para reportes (PDF/Excel) |
| `DataTableService` | Procesamiento de datos para tablas dinámicas |

---

## 8. Reportes y exportaciones (app/Exports/)

Cada reporte tiene su clase Export en `app/Exports/`:

| Export | Reporte | Ruta PDF | Ruta Excel |
|--------|---------|----------|------------|
| `MedicosPorEspecialidadExport` | Médicos por especialidad | `reportes/pdf/medicos-por-especialidad` | `reportes/excel/medicos-por-especialidad/excel` |
| `ProcedenciaPacientesExport` | Procedencia de pacientes | `reportes/pdf/procedencia-pacientes` | `reportes/excel/procedencia-pacientes/excel` |
| `MovimientoConsultasExport` | Movimiento de consultas | `reportes/pdf/movimiento-consultas/pdf` | `reportes/excel/movimiento-consultas/excel` |
| `MovimientoConsultaAroExport` | Movimiento ARO (embarazadas) | `reportes/pdf/movimiento-consulta-aro/pdf` | `reportes/excel/movimiento-consulta-aro/excel` |
| `CausasPrincipalesExport` | Causas principales (morbilidad) | `reportes/pdf/causas-principales/pdf` | `reportes/excel/causas-principales/excel` |
| `InasistenciasExport` | Inasistencias | `reportes/pdf/inasistencias-citas/pdf` | `reportes/excel/inasistencias-citas/excel` |
| `ProductividadMedicoExport` | Productividad de médicos | `reportes/pdf/productividad-medico/pdf` | `reportes/excel/productividad-medico/excel` |
| `CitasSinDiagnosticoExport` | Citas sin diagnóstico | `reportes/pdf/citas-sin-diagnostico/pdf` | `reportes/excel/citas-sin-diagnostico/excel` |
| `EficienciaAtencionExport` | Eficiencia de atención | `reportes/pdf/eficiencia-atencion/pdf` | `reportes/excel/eficiencia-atencion/excel` |
| `MorbilidadExport` | Morbilidad | (ver MorbilidadController) | |

> **⚠️ Problema:** `ReporteController@getProcedenciaData()` contiene IDs de distrito hardcodeados (`<= 5`, `== 6`, `999`, `1000`). Son específicos de la BD actual y fallarán tras restaurar backups.

---

## 9. Configuración importante

### `config/citas.php`
Define `especialidades_solo_femenino` — lista de especialidades donde pacientes de sexo masculino no pueden agendar ni atender citas:
- Ginecologia Oncologica
- Menopausia
- Patologia Vulva/Vaginal
- Patologia Medica del Embarazo
- Aro (Embarazados)
- Pre Operatorio Emb

### `config/auth.php`
Usa guards web. Eloquent User provider.

### `config/audit.php`
Configuración de OwenIt Auditing para modelos auditables (Cita y otros críticos).

---

## 10. Lanzador de escritorio (`launcher/`)

Permite arrancar el sistema con un doble clic en Windows:

| Archivo | Descripción |
|---------|-------------|
| `SAGECIM.vbs` | Punto de entrada que ejecuta PowerShell oculto |
| `sagecim-launcher.ps1` | Verifica PostgreSQL, inicia `php artisan serve`, abre navegador, limpia procesos al cerrar |
| `config.json` | Config editable: puerto, rutas de PHP/app, navegador, puerto de PostgreSQL |

Endpoint relacionado: `POST /lanzador/cerrar-sesion` (`LanzadorController@cerrarSesion`).

---

## 11. Base de datos y migraciones

- Motor: **PostgreSQL 17**
- Migraciones están fechadas desde `2026-04-28` hasta `2026-09-04`
- Últimas migraciones: `lanzador_sesiones`, `estado` en pacientes, `historico_numeros`, `numero_expediente` nullable, `enable_unaccent_extension`, `add_trgm_indexes_pacientes`
- Migración `2026_07_21_153000_add_case_insensitive_indexes`: agrega índices únicos case-insensitive (razón por la que se debe usar `migrate:fresh` al sembrar)
- Seeds disponibles: `DatabaseSeeder` llama a todos los seeders en orden

### Índices de búsqueda (pacientes)

El DataTable de pacientes usa `unaccent(columna) ILIKE unaccent(?)` para búsquedas insensibles a acentos y mayúsculas. Sin índices de expresión, PostgreSQL hace un **seq scan** completo en cada búsqueda.

Las migraciones recientes agregan:

| Migración | Extensión | Índices creados |
|-----------|-----------|-----------------|
| `2026_09_04_000000_enable_unaccent_extension` | `unaccent` | — |
| `2026_09_04_000001_add_trgm_indexes_pacientes` | `pg_trgm` | GIN sobre `unaccent(nombre)` y `unaccent(apellido)` con `gin_trgm_ops` |

> **Nota:** Crear extensiones en PostgreSQL requiere privilegios de superusuario o ser miembro de `pg_database_owner`.

> **Por qué `pg_trgm` y no B-tree:** los índices B-tree no sirven para patrones `%texto%` con comodín al inicio. Los índices GIN con `gin_trgm_ops` permiten que PostgreSQL use el índice para `ILIKE` con comodines, evitando el escaneo secuencial.

> **Simplificación:** solo se indexan `nombre` y `apellido` porque son las columnas donde los acentos y mayúsculas presentan el mayor inconveniente. `cedula` y `direccion` mantienen `ILIKE` convencional.

---

## 12. Credenciales por defecto (Seed)

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Administrador | `admin@hospital.gob.ve` | `password` |
| Usuario | `empleado1@hospital.gob.ve` … `empleado5@hospital.gob.ve` | `password` |

> Cambiar en producción.

---

## 13. Comandos útiles

```bash
# Instalación completa
composer install && cp .env.example .env && php artisan key:generate && php artisan migrate:fresh --seed && npm install && npm run build

# Servidor
php artisan serve

# Tests
composer test
# o: php artisan test

# Reset BD limpio
php artisan migrate:fresh --seed

# Solo seeders
php artisan db:seed

# Pint (formateo de código)
vendor/bin/pint

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 14. Problemas conocidos (prioridad para correcciones)

Ver `analisisProblemas-23-06-2026.md` para el reporte completo. Resumen:

### Críticos 🔴
1. **Sin autorización en controladores** — ningún `$this->authorize()` ni Policies.
2. **Rate limiter de login no implementado** — `RateLimiter` importado pero no usado.

### Altos 🟠
3. **Tres rutas públicas sin `auth`** — calendario y municipios.
4. **Ruta duplicada de `Citas.show`** — eliminar `/Citas/{id}/show`.

### Medios 🟡
5. **Controladores vacíos** — `AtencionMedicaController`, `ExpedienteController`.
6. **Sin `declare(strict_types=1)`** en ningún archivo PHP.
7. **Solo 1 de 16 modelos usa `HasFactory`** — agregar al resto.
8. **Sin `$casts` en 15 de 16 modelos** — fechas, booleanos y enteros sin castear.
9. **Sin try/catch en 20 de 23 controladores** — errores 500 sin manejo.
10. **`Paciente::Expediente()` viola PSR-1** — renombrar a `expediente()`.
11. **Middleware `can:Cita,Pacientes` mal configurado**.
12. **IDs de distrito hardcodeados en Reportes** (`<= 5`, `== 6`, `999`, `1000`).
13. **Nombres de ruta inconsistentes** (PascalCase vs lowercase).
14. **`$table` no definido en varios modelos**.
15. **Rutas API sin nombre** (no se puede usar `route()`).

### ✅ Corregido recientemente
- **Buscador de pacientes insensible a acentos/mayúsculas:** se agregó `unaccent()` en las 4 columnas de búsqueda del DataTable (`nombre`, `apellido`, `cedula`, `direccion`) junto con índices GIN `pg_trgm` para evitar seq scans en tablas grandes.

### Informativos 🔵
16. Pruebas automatizadas limitadas.

---

## 15. Convenciones y buenas prácticas para este proyecto

1. **Siempre usar `migrate:fresh --seed`** al sembrar (índices case-insensitive).
2. **Validar autorización** en controladores con `$this->authorize()` además del middleware de ruta.
3. **Evitar hardcodear IDs** de distrito, especialidad, etc. Usar consultas dinámicas.
4. **Usar minúsculas en rutas** (`/citas` no `/Citas`).
5. **Agregar `declare(strict_types=1)`** en archivos PHP nuevos.
6. **Agregar `HasFactory` y `$casts`** a modelos nuevos o existentes.
7. **Envuelve escrituras en `try/catch` con transacciones** (`DB::beginTransaction()` / `rollBack()`).
8. **Los reportes PDF usan `Membrete`** (`app/Support/Membrete.php`) para el encabezado.
9. **Las notificaciones usan SweetAlert** (`realrashid/sweet-alert`) en el frontend.
10. **Auditoría automática** en modelos que implementan `OwenIt\Auditing\Contracts\Auditable` (ej. `Cita`).

---

## 16. Archivos de contexto adicionales

- `README.md` — Documentación de usuario e instalación.
- `analisisProblemas-23-06-2026.md` — Análisis detallado de deuda técnica y bugs.
- `leeme.txt` — Nota adicional de instrucciones.
- `hospitalbackup-*.sql` — Backups de la base de datos. Ya no se versionan (quedan solo en el equipo local, ignorados por `.gitignore`).
