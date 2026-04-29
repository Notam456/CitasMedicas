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
    Schema::create('expedientes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('paciente_id')->unique()->constrained('pacientes')->cascadeOnDelete();
    $table->string('numero_expediente')->unique();
    $table->date('fecha_apertura');
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
