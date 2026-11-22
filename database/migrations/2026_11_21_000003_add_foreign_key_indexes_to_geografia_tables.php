<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PostgreSQL no indexa las claves foraneas automaticamente. Estos son los
     * joins del reporte de procedencia (municipios -> distritos y
     * parroquias -> municipios); sin indice, cada reporte hace seq scan.
     */
    public function up(): void
    {
        Schema::table('municipios', function (Blueprint $table) {
            $table->index('distrito_id', 'idx_municipios_distrito');
        });

        Schema::table('parroquias', function (Blueprint $table) {
            $table->index('municipio_id', 'idx_parroquias_municipio');
        });
    }

    public function down(): void
    {
        Schema::table('municipios', function (Blueprint $table) {
            $table->dropIndex('idx_municipios_distrito');
        });

        Schema::table('parroquias', function (Blueprint $table) {
            $table->dropIndex('idx_parroquias_municipio');
        });
    }
};
