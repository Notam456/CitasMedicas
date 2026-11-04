<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crea índices de expresión con pg_trgm sobre unaccent.
     * Se usa una función wrapper IMMUTABLE porque PostgreSQL
     * requiere que las funciones en expresiones de índice sean IMMUTABLE.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Asegurar que la extensión unaccent esté disponible en el esquema público
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        // Función wrapper IMMUTABLE para unaccent
        DB::statement('
            CREATE OR REPLACE FUNCTION unaccent_immutable(text)
            RETURNS text
            LANGUAGE sql
            IMMUTABLE
            AS $$
                SELECT public.unaccent($1)
            $$;
        ');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_pacientes_nombre_unaccent_trgm
            ON pacientes USING GIN (unaccent_immutable(nombre) gin_trgm_ops)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_pacientes_apellido_unaccent_trgm
            ON pacientes USING GIN (unaccent_immutable(apellido) gin_trgm_ops)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS idx_pacientes_nombre_unaccent_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_pacientes_apellido_unaccent_trgm');
        DB::statement('DROP FUNCTION IF EXISTS unaccent_immutable(text)');
        DB::statement('DROP EXTENSION IF EXISTS pg_trgm');
        DB::statement('DROP EXTENSION IF EXISTS unaccent');
    }
};
