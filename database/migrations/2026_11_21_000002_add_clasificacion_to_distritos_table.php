<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las reglas de presentacion del reporte de procedencia dejan de estar
     * en el codigo (comparacion por nombre) y pasan a ser datos:
     *
     *  - es_agrupador: el distrito es una bolsa de agrupacion (ej. "Otros Estados")
     *    y no se desglosa por municipio.
     *  - mostrar_desglose: permite ocultar el desglose a cualquier distrito.
     *  - orden: orden de presentacion, independiente del alfabeto.
     */
    public function up(): void
    {
        Schema::table('distritos', function (Blueprint $table) {
            $table->boolean('es_agrupador')->default(false);
            $table->boolean('mostrar_desglose')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
        });

        $otrosEstados = config('citas.distrito_otros_estados', 'Otros Estados');

        DB::table('distritos')
            ->whereRaw('lower(nombre) = lower(?)', [$otrosEstados])
            ->update([
                'es_agrupador' => true,
                'mostrar_desglose' => false,
                'orden' => 999,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('distritos', function (Blueprint $table) {
            $table->dropColumn(['es_agrupador', 'mostrar_desglose', 'orden']);
        });
    }
};
