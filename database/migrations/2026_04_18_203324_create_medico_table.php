<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico', function (Blueprint $table) {
            $table->id('id_medico');
            $table->string('nombres', 150);
            $table->string('apellidos', 150);
            $table->string('cedula', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->foreignId('id_especialidad')
                  ->constrained('especialidad', 'id_especialidad')
                  ->onDelete('restrict');
            // Índices
            $table->index('cedula', 'idx_medico_cedula');
            $table->boolean('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico');
    }
};
