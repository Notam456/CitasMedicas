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
    Schema::create('pacientes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parroquia_id')->constrained('parroquias'); 
    $table->string('nombre');
    $table->string('apellido');
    $table->string('cedula')->unique();
    $table->date('fecha_nacimiento');
    $table->string('telefono');
    $table->string('direccion');
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
