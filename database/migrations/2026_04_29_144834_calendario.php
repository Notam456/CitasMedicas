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
    Schema::create('calendarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
    $table->date('fecha');
    $table->time('hora_inicio');
    $table->time('hora_fin');
    $table->integer('cupos_sucesivos');
    $table->integer('cupos_primera_vez');
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
