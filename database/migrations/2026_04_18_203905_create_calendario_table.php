<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendario', function (Blueprint $table) {
            $table->id('id_calendario');
            $table->foreignId('id_medico')
                  ->constrained('medico', 'id_medico')
                  ->onDelete('cascade');
            $table->foreignId('id_especialidad')
                  ->constrained('especialidad', 'id_especialidad')
                  ->onDelete('restrict');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('cupos_disponibles')->unsigned()->check('cupos_disponibles >= 0');
            // Índices
            $table->index('fecha', 'idx_calendario_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario');
    }
};
