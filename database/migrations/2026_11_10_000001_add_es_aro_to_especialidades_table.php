<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bandera explicita para la especialidad ARO.
     * Evita depender del nombre literal en los reportes.
     */
    public function up(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->boolean('es_aro')->default(false)->after('estado');
        });

        DB::table('especialidades')
            ->where('nombre', config('citas.especialidad_aro', 'Aro (Embarazados)'))
            ->update(['es_aro' => true]);
    }

    public function down(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropColumn('es_aro');
        });
    }
};
