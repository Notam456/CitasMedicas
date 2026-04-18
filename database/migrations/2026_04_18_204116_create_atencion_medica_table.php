<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atencion_medica', function (Blueprint $table) {
            $table->id('id_atencion');
            $table->foreignId('id_cita')
                  ->unique()  // relación 1:1
                  ->constrained('cita', 'id_cita')
                  ->onDelete('cascade');
            $table->text('diagnostico')->nullable();
            $table->boolean('asistio')->default(false);
            // Índice
            $table->index('id_cita', 'idx_atencion_cita');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atencion_medica');
    }
};
