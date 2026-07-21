<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horario_medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('CASCADE');
            $table->unsignedTinyInteger('dia_semana'); // 1 = Lunes, 7 = Domingo
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->timestamps();

            // Evitar duplicados del mismo día para un médico
            $table->unique(['medico_id', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_medico');
    }
};
