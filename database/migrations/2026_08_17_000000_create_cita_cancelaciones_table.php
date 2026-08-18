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
        Schema::create('cita_cancelaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->unique()->constrained('citas')->onDelete('cascade');
            $table->enum('motivo', ['ausencia_paciente', 'ausencia_medico']);
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_cancelacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita_cancelaciones');
    }
};
