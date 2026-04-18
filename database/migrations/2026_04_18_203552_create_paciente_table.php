<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente', function (Blueprint $table) {
            $table->id('id_paciente');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('cedula', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->foreignId('id_representante')
                  ->nullable()
                  ->constrained('representante', 'id_representante')
                  ->onDelete('set null');
            // Índices
            $table->index('cedula', 'idx_paciente_cedula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente');
    }
};
