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
    Schema::create('citas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('paciente_id')->constrained('pacientes');
    $table->foreignId('calendario_id')->constrained('calendarios');
    $table->foreignId('user_id')->constrained();
    $table->date('fecha_registro');
    $table->date('fecha_cita');
    $table->string('estado');
    $table->string('tipo_paciente');
    $table->text('observacion')->nullable();
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
