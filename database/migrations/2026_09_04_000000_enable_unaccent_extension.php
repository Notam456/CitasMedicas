<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Habilita la extensión unaccent de PostgreSQL para permitir búsquedas
     * insensibles a acentos (ej. "Maria" encuentra "María").
     *
     * Requiere que el usuario de BD tenga privilegios para crear extensiones
     * (superusuario o miembro de pg_database_owner en algunos entornos).
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP EXTENSION IF EXISTS unaccent');
    }
};
