# Análisis de Problemas del Sistema — CitasMedicas

**Fecha:** 23 de junio de 2026
**Rama:** `main`
**Propósito:** Documentar los hallazgos del repaso general al código fuente para priorizar correcciones futuras.

---

## 🔴 Críticos

### 1. Ausencia total de autorización en controladores

**Archivos afectados:** `app/Http/Controllers/RoleController.php`, `app/Http/Controllers/UserController.php` y todos los demás controladores.

**Descripción:** Ningún controlador del sistema utiliza `$this->authorize()`, policies, o middleware de permisos en su constructor. La única capa de seguridad son los middleware `auth` y `can:` definidos en `routes/web.php`. Sin embargo, `RoleController` y `UserController` no tienen ninguna verificación de permisos, lo que permite que **cualquier usuario autenticado** pueda:

- Crear, editar o eliminar usuarios
- Asignar cualquier rol (incluyendo `administrador`)
- Crear y modificar roles con cualquier permiso

**Riesgo:** Escalada de privilegios total. Un usuario con acceso básico puede autoconcederse permisos de administrador.

**Solución propuesta:** Agregar middleware `can:Usuarios` a las rutas de `UserController` y `RoleController` (ya existen en el archivo de rutas, pero los controladores no los refuerzan). Implementar Policies de Laravel para cada módulo.

---

### 2. Rate limiter de login no implementado

**Archivo afectado:** `app/Http/Controllers/LoginController.php`

**Descripción:** El archivo importa `Illuminate\Cache\RateLimiter` pero nunca lo utiliza. El método `login()` no verifica intentos fallidos ni bloquea IPs después de múltiples intentos. La ruta `POST /iniciar-sesion` tiene middleware `throttle:10,1` (10 intentos por minuto), pero no hay lógica de bloqueo progresivo ni notificación al usuario.

**Riesgo:** Ataque de fuerza bruta al formulario de inicio de sesión.

**Solución propuesta:** Implementar `RateLimiter::hit()` y `RateLimiter::tooManyAttempts()` en el método `login()`, o confiar en el middleware `throttle:` con mensajes de error adecuados.

---

## 🟠 Altos

### 3. Rutas públicas sin autenticación

**Archivo afectado:** `routes/web.php`

**Descripción:** Tres rutas carecen del middleware `auth` y son accesibles sin iniciar sesión:

| URI | Controlador | Línea |
|-----|------------|-------|
| `GET /calendario/medicos/{especialidad}` | `CalendarioController@getMedicos` | 93 |
| `GET /calendario/eventos` | `CalendarioController@getDatosMes` | 94 |
| `GET /municipios-por-estado/{estado_id}` | `ParroquiaController@getMunicipiosPorEstado` | 99 |

**Riesgo:** Las rutas de calendario exponen la disponibilidad de médicos y eventos del hospital al público general. La ruta de municipios expone datos geográficos internos.

**Solución propuesta:** Agregar middleware `auth` a estas rutas, o moverlas dentro del grupo de middleware correspondiente.

---

## 🟡 Medios

### 4. Ruta duplicada de Citas.show

**Archivo afectado:** `routes/web.php` (líneas 89-90)

**Descripción:** Existen dos rutas que apuntan al mismo método `CitaController@show`:

```php
Route::resource('Citas', CitaController::class);  // genera GET /Citas/{cita}
Route::get('/Citas/{id}/show', [CitaController::class, 'show']);  // redundante
```

**Riesgo:** La segunda ruta es redundante y puede causar confusión. El parámetro `{id}` no sigue el mismo nombre que el de resource (`{cita}`).

**Solución propuesta:** Eliminar la ruta duplicada en la línea 90.

---

### 5. Controladores vacíos (stubs)

**Archivos afectados:**
- `app/Http/Controllers/AtencionMedicaController.php`
- `app/Http/Controllers/ExpedienteController.php`

**Descripción:** Ambos controladores tienen todos sus métodos vacíos (sin implementación). Las rutas correspondientes existen pero devuelven errores o páginas en blanco.

**Riesgo:** Funcionalidad prometida pero no implementada. Si los usuarios intentan acceder a estas secciones, obtendrán errores inesperados.

**Solución propuesta:** Implementar la lógica o eliminar los controladores y rutas si no son necesarios.

---

### 6. Ausencia de `declare(strict_types=1)`

**Archivos afectados:** Todos los archivos PHP del proyecto (16 modelos, 23 controladores, migraciones, seeders, etc.)

**Descripción:** Ningún archivo PHP declara `declare(strict_types=1)` al inicio. Esto permite coerción de tipos silenciosa, que puede ocultar bugs sutiles.

**Ejemplo:** Pasar un string `"5"` donde se espera un `int` 5 no generará error.

**Solución propuesta:** Agregar `declare(strict_types=1)` después de `<?php` en todos los archivos.

---

### 7. Solo 1 de 16 modelos usa `HasFactory`

**Archivos afectados:** Todos los modelos excepto `app/Models/User.php`

**Descripción:** De los 16 modelos del sistema, solo `User` implementa el trait `HasFactory`. Esto impide usar Laravel factories para generar datos de prueba en los 15 modelos restantes.

**Riesgo:** Las pruebas unitarias y de integración no pueden generar datos realistas fácilmente. Las semillas (seeders) deben escribirse manualmente.

**Solución propuesta:** Agregar `use HasFactory` a los modelos: `Cita`, `Calendario`, `Medico`, `Paciente`, `Especialidad`, `Estado`, `Municipio`, `Parroquia`, `Distrito`, `Expediente`, `Medicamento`, `Patologia`, `CitaPatologia`, `CitaTratamiento`, `CitaReferencia`.

---

### 8. Sin casts en 15 de 16 modelos

**Archivos afectados:** Todos los modelos excepto `app/Models/User.php`

**Descripción:** Solo `User` define una propiedad/método de casts. Los demás modelos no convierten automáticamente tipos de datos como fechas, enteros o booleanos al recuperarlos de la base de datos.

**Ejemplos de campos sin cast:**
- `fecha` y `hora_inicio`/`hora_fin` en `Calendario` (deberían ser `date`/`datetime`)
- `fecha_nacimiento` en `Paciente` (debería ser `date`)
- `fecha_registro` y `fecha_cita` en `Cita` (deberían ser `datetime`)
- `estado` en `Especialidad` (debería ser `boolean`, actualmente tiene `$attributes['estado'] = true` pero sin cast)
- `cupos_primera_vez` y `cupos_sucesivos` en `Calendario` (deberían ser `integer`)
- `reagendada_contador` en `Cita` (debería ser `integer`)

**Riesgo:** Los valores booleanos pueden devolverse como `1`/`0` en lugar de `true`/`false`. Las fechas pueden ser strings en lugar de objetos Carbon.

**Solución propuesta:** Agregar `protected $casts = [...]` a cada modelo.

---

### 9. Sin try/catch en 20 de 23 controladores

**Archivos afectados:** La mayoría de los controladores CRUD

**Descripción:** Solo `CalendarioController`, `CitaController` y `DiagnosticoController` utilizan bloques `try/catch` con transacciones de base de datos. Los 20 controladores restantes no manejan excepciones, por lo que cualquier error de base de datos (violación de clave foránea, unique constraint) resultará en una página de error 500 con detalles técnicos.

**Riesgo:** Mala experiencia de usuario, posible exposición de información interna en entornos de producción si `APP_DEBUG=true`.

**Solución propuesta:** Envolver operaciones de escritura en `try/catch` con `DB::beginTransaction()`/`DB::rollBack()` y mensajes de error amigables.

---

### 10. `Paciente::Expediente()` viola PSR-1

**Archivo afectado:** `app/Models/Paciente.php`

**Descripción:** El método de relación `Expediente()` comienza con mayúscula. PSR-1 exige que los métodos estén en `camelCase` (minúscula inicial). Laravel puede resolver la relación correctamente porque usa `$this->Expediente()` en la llamada, pero el nombre incorrecto puede causar problemas con resolución dinámica de relaciones y herramientas de análisis estático.

**Solución propuesta:** Renombrar a `expediente()`.

---

### 11. Middleware `can:Citas,Pacientes` mal configurado

**Archivo afectado:** `routes/web.php` (línea 78)

**Descripción:** El middleware `can:Citas,Pacientes` intenta verificar dos permisos separados usando comas. Sin embargo, la sintaxis del middleware `can` de Laravel es `can:habilidad,parametro_de_ruta`. Esto no crea una verificación AND/OR de dos permisos; en su lugar, busca una gate llamada `Citas` y pasa `Pacientes` como argumento, lo cual es incorrecto.

**Riesgo:** Las rutas protegidas por este middleware (API de municipios/parroquias) pueden estar mal protegidas.

**Solución propuesta:** Usar middleware personalizado o separar en grupos con `can:Citas` y `can:Pacientes`.

---

### 12. IDs de distritos hardcodeados en Reportes

**Archivo afectado:** `app/Http/Controllers/ReporteController.php` — método `getProcedenciaData()`

**Descripción:** El método contiene IDs numéricos hardcodeados como `$distritoId <= 5`, `$distritoId == 6`, `id => 999`, `id => 1000`. Estos valores son específicos de la base de datos actual y fallarán si los datos cambian o se restauran desde otro backup.

**Riesgo:** Los reportes de procedencia de pacientes pueden generar resultados incorrectos después de una migración o restauración de datos.

**Solución propuesta:** Reemplazar IDs hardcodeados con consultas dinámicas o configuraciones.

---

### 13. Nombre inconsistente de rutas (PascalCase vs lowercase)

**Archivo afectado:** `routes/web.php`

**Descripción:** La mayoría de las rutas usan minúsculas (`/paciente`, `/medicos`, `/especialidades`), pero las rutas de citas usan PascalCase (`/Citas`, `/Citas/create`). Esto es inconsistente y puede causar confusión.

**Solución propuesta:** Unificar a minúsculas (`/citas`).

---

### 14. `$table` no definido en varios modelos

**Archivos afectados:** `Paciente.php`, `Distrito.php`, `Expediente.php`, `Medicamento.php`, `Patologia.php`, `CitaReferencia.php`

**Descripción:** Estos modelos no declaran `protected $table`, por lo que Laravel infiere el nombre de la tabla a partir del nombre de la clase (convención Snake Case plural). Esto funciona mientras los nombres de clase sigan la convención, pero es frágil ante renombrados.

---

### 15. Rutas API sin nombre

**Archivo afectado:** `routes/web.php`

**Descripción:** Varias rutas API carecen de `->name()`:

- `GET /api/estados`
- `GET /api/municipios/{estado_id}`
- `GET /api/parroquias/{municipio_id}`
- `GET /api/especialidades/{id}/medicos`
- `GET /api/medicos/{medico_id}/disponibilidad`
- `GET /api/medicamentos`
- `GET /api/patologias/por-cita/{cita}`

**Riesgo:** No se pueden generar URLs para estas rutas usando `route()`, lo que dificulta el mantenimiento.

---

## 🔵 Informativos

### 16. Pruebas automatizadas limitadas

**Archivo afectado:** `tests/`

**Descripción:** Solo existen los dos `ExampleTest` que vienen por defecto con Laravel. No hay pruebas unitarias, de feature ni de integración en la rama `main`. Las 40 pruebas creadas anteriormente están en la rama `Pruebas_del_sistema` y no han sido fusionadas.

**Solución propuesta:** Fusionar las pruebas de `Pruebas_del_sistema` a `main` después de verificar que todas pasan.

---

### 17. Sin FormRequest classes

**Descripción:** Toda la validación de entrada se realiza inline en los controladores usando `$request->validate()`. Si bien esto funciona, el uso de FormRequest classes permitiría reutilizar la validación, separar concerns y facilitar las pruebas.

---

### 18. Sin políticas de autorización (Policies)

**Descripción:** No existen archivos de Policy en `app/Policies/`. La autorización se maneja exclusivamente a través de middleware `can:` en rutas y roles de Spatie.

---

## Resumen

| Severidad | Cantidad |
|-----------|----------|
| 🔴 Crítico | 2 |
| 🟠 Alto | 1 |
| 🟡 Medio | 12 |
| 🔵 Informativo | 3 |
| **Total** | **18** |
