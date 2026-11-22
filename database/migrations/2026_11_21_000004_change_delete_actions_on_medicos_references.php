<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Borrar un medico arrastraba sus calendarios y, con ellos, sus citas
     * (citas.calendario_id tambien es CASCADE). Al borrar una especialidad
     * el efecto es mayor: especialidad -> medicos -> calendarios -> citas,
     * mas patologias y sus diagnosticos registrados.
     *
     * RESTRICT obliga a reasignar o archivar antes de borrar, en vez de
     * perder historia clinica en silencio. Cada FK se elimina en un bloque y
     * se recrea en otro para no depender del orden de compilacion de
     * comandos del Blueprint.
     */
    private const RESTRINGIDAS = [
        ['calendarios', 'medico_id', 'medicos'],
        ['suspensiones_medicos', 'medico_id', 'medicos'],
        ['horario_medico', 'medico_id', 'medicos'],
        ['medicos', 'especialidad_id', 'especialidades'],
        ['calendarios', 'especialidad_id', 'especialidades'],
        ['patologias', 'especialidad_id', 'especialidades'],
    ];

    public function up(): void
    {
        foreach (self::RESTRINGIDAS as [$tabla, $columna]) {
            Schema::table($tabla, function (Blueprint $table) use ($columna) {
                $table->dropForeign([$columna]);
            });
        }

        foreach (self::RESTRINGIDAS as [$tabla, $columna, $referencia]) {
            Schema::table($tabla, function (Blueprint $table) use ($columna, $referencia) {
                $table->foreign($columna)->references('id')->on($referencia)->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (self::RESTRINGIDAS as [$tabla, $columna]) {
            Schema::table($tabla, function (Blueprint $table) use ($columna) {
                $table->dropForeign([$columna]);
            });
        }

        foreach (self::RESTRINGIDAS as [$tabla, $columna, $referencia]) {
            Schema::table($tabla, function (Blueprint $table) use ($columna, $referencia) {
                $table->foreign($columna)->references('id')->on($referencia)->cascadeOnDelete();
            });
        }
    }
};
