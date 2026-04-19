<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente', function (Blueprint $table) {
            $table->id('id_expediente');
            $table->foreignId('id_paciente')
                  ->constrained('paciente', 'id_paciente')
                  ->onDelete('cascade');
            $table->string('numero_expediente', 50)->unique();
            $table->date('fecha_registro')->useCurrent();
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->string('estado', 50)->nullable();
            $table->text('observacion')->nullable();
            // Índices
            $table->index('id_paciente', 'idx_expediente_paciente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente');
    }
};
