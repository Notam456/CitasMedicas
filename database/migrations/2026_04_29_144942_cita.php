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
    $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('CASCADE');
    $table->foreignId('calendario_id')->constrained('calendarios')->onDelete('CASCADE');
    $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
    $table->date('fecha_registro');
    $table->date('fecha_cita')->index();
    $table->string('estado')->index();
    $table->string('tipo_paciente');
    $table->text('observacion')->nullable();
    $table->text('diagnostico_libre')->nullable();
    $table->unsignedBigInteger('atendido_por')->nullable()->after('user_id');
    $table->foreign('atendido_por')->references('id')->on('users')->onDelete('set null');
    $table->unique(['calendario_id', 'paciente_id'], 'citas_unique_activas');
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
