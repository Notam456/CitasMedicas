<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->index(['estado', 'fecha_cita'], 'idx_citas_estado_fecha');
            $table->index(['paciente_id', 'fecha_cita'], 'idx_citas_paciente_fecha');
            $table->index(['fecha_cita', 'tipo_paciente'], 'idx_citas_fecha_tipo');
        });

        Schema::table('calendarios', function (Blueprint $table) {
            $table->index(['medico_id', 'fecha'], 'idx_calendarios_medico_fecha');
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->index('parroquia_id', 'idx_pacientes_parroquia');
        });

        Schema::table('cita_cancelaciones', function (Blueprint $table) {
            $table->index('motivo', 'idx_cita_cancelaciones_motivo');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex('idx_citas_estado_fecha');
            $table->dropIndex('idx_citas_paciente_fecha');
            $table->dropIndex('idx_citas_fecha_tipo');
        });

        Schema::table('calendarios', function (Blueprint $table) {
            $table->dropIndex('idx_calendarios_medico_fecha');
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropIndex('idx_pacientes_parroquia');
        });

        Schema::table('cita_cancelaciones', function (Blueprint $table) {
            $table->dropIndex('idx_cita_cancelaciones_motivo');
        });
    }
};
