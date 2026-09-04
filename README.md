<div align="center">

<img src="public/assets/img/sagecim_icon.png" alt="SAGECIM" width="110" />

# SAGECIM

**Sistema de Gestión de Citas Médicas**

[![PHP](https://img.shields.io/badge/PHP-%5E8.2-8892BF?logo=php&logoColor=white)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-17-336791?logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

</div>

---

## Descripción

**SAGECIM** es una aplicación web para la administración de consultas médicas y citas de un centro de salud. Permite gestionar el catálogo de especialidades y médicos, programar citas con control de disponibilidad y cupos, registrar la atención y los diagnósticos de cada paciente, y generar reportes operativos y gerenciales con exportación a **PDF** y **Excel**.

El proyecto incluye además un **lanzador de escritorio** que arranca la aplicación de forma transparente en cualquier equipo, con un solo doble clic.

## Características principales

- **Autenticación y control de acceso** mediante roles y permisos (Spatie Permission).
- **Catálogos (maestros):** especialidades, médicos, pacientes, patologías y procedencia (estados, municipios, parroquias y distritos).
- **Agenda y planificación:** calendario de disponibilidad por médico, cupos y bloqueos.
- **Atención de citas:** lista de pendientes, registro de diagnósticos y morbilidad por cita.
- **Suspensiones de médicos** con asignación de suplentes.
- **Reportes en PDF y Excel:** médicos por especialidad, procedencia de pacientes, movimiento de consultas, causas principales, inasistencias, productividad de médicos, citas sin diagnóstico y eficiencia de atención.
- **Notificaciones** internas de la plataforma.
- **Auditoría de movimientos** sobre las entidades críticas.
- **Lanzador de escritorio** (`.vbs` + PowerShell) para arrancar el sistema con un clic en equipos locales.

## Stack tecnológico

| Capa | Tecnología |
| --- | --- |
| Backend | PHP ^8.2, Laravel 12 |
| Base de datos | PostgreSQL 17, Eloquent ORM, migraciones y seeders |
| Frontend | Blade, Tailwind CSS 4, Vite |
| Paquetes clave | spatie/laravel-permission, owen-it/laravel-auditing, maatwebsite/excel, carlos-meneses/laravel-mpdf, realrashid/sweet-alert |

## Requisitos previos

- **PHP** ^8.2 con las extensiones habilitadas en `php.ini`:
  - `pdo_pgsql`
  - `gd`
  - `zip`
  - `xls` (si aplica)
- **Composer**
- **Node.js** y **npm**
- **PostgreSQL** (versión recomendada: 17.6.1)

## Instalación y puesta en marcha

Clona el repositorio y ejecuta los siguientes pasos en la raíz del proyecto:

### 1. Crear la base de datos

Crea una base de datos PostgreSQL (por ejemplo `hospital`) y anota tus credenciales.

### 2. Configurar el entorno

```bash
# Instalar dependencias de PHP
composer install

# Copiar el archivo de variables de entorno
cp .env.example .env
```

Edita `.env` y configura la conexión a PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hospital
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

> **Importante:** `.env` contiene información sensible y está excluido del control de versiones (`.gitignore`). No debe compartirse.

### 3. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 4. Migrar y sembrar la base de datos

```bash
php artisan migrate:fresh --seed
```

> **Nota:** usa siempre `migrate:fresh` (no `migrate`) al sembrar, para que las tablas se creen limpias. Ejecutar el seeder sobre una base ya poblada sin `fresh` provocará errores de clave duplicada (índices únicos *case-insensitive*).

### 5. Compilar los assets frontend

```bash
npm install
npm run build
```

### 6. Iniciar el servidor

```bash
php artisan serve
```

Accede desde el navegador en `http://127.0.0.1:8000`.

## Credenciales por defecto (seed)

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@hospital.gob.ve` | `password` |
| Usuario | `empleado1@hospital.gob.ve` a `empleado5@hospital.gob.ve` | `password` |

> Estas credenciales son generadas por `UserSeeder` y deben cambiarse en un entorno de producción.

## Lanzador de escritorio

El directorio `launcher/` contiene un lanzador para arrancar el sistema en equipos locales con un doble clic, sin abrir terminales:

| Archivo | Descripción |
| --- | --- |
| `SAGECIM.vbs` | Punto de entrada que ejecuta el script de PowerShell de forma oculta. |
| `sagecim-launcher.ps1` | Verifica PostgreSQL, inicia `php artisan serve`, abre el navegador en una ventana dedicada y limpia los procesos al cerrarla. |
| `config.json` | Configuración editable: puerto, rutas de PHP/la aplicación, navegador y puerto de PostgreSQL. |

**Requisitos:** tener PostgreSQL en ejecución y PowerShell disponible en el equipo. Al cerrar la ventana del navegador, el lanzador cierra la sesión activa y detiene el servidor PHP.

## Ejecutar los tests

```bash
composer test
```

## Estructura del proyecto

```
app/            Lógica de la aplicación (controladores, modelos, middleware)
routes/         Definición de rutas web
database/       Migraciones, factores y seeders
resources/views Vistas Blade y assets
launcher/       Lanzador de escritorio (VBS + PowerShell)
tests/          Pruebas automatizadas
```

## Contribución

Las contribuciones son bienvenidas. Por favor, abre un *pull request* describiendo los cambios propuestos.

## Autores

José Yajure, Daniel Alejos, Roberto Vielma, Yhoenyer Alvarado, Jose Gonzalez.

## Licencia

Este proyecto está licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).
