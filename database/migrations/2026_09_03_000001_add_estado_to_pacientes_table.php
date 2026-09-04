<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('estado')->default('activo')->after('sexo');
            $table->string('estado_motivo')->nullable()->after('estado');
            $table->date('fecha_baja')->nullable()->after('estado_motivo');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['estado', 'estado_motivo', 'fecha_baja']);
        });
    }
};
