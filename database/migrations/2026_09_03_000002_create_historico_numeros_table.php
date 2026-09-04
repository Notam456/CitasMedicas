<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_numeros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('numero_expediente');
            $table->string('motivo')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_liberacion')->nullable();
            $table->boolean('vigente')->default(false);
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('numero_expediente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_numeros');
    }
};
