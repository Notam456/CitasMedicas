<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->id('id_cita');
            $table->foreignId('id_paciente')
                  ->constrained('paciente', 'id_paciente')
                  ->onDelete('cascade');
            $table->foreignId('id_calendario')
                  ->constrained('calendario', 'id_calendario')
                  ->onDelete('restrict');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->onDelete('set null');
                  
            $table->timestamp('fecha_registro')->useCurrent();
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->string('estado', 50)->default('pendiente');
            $table->text('observacion')->nullable();
            // Índices
            $table->index('fecha_cita', 'idx_cita_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};
