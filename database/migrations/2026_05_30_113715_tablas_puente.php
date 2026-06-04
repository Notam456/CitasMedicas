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
        Schema::create('cita_patologias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('patologia_id');
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('patologia_id')->references('id')->on('patologias')->onDelete('cascade');
            $table->unique(['cita_id', 'patologia_id']);
        });

        // 4. Crear tabla cita_referencias
        Schema::create('cita_referencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('especialidad_id');
            $table->text('observaciones')->nullable();
            $table->date('fecha_referencia')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('especialidad_id')->references('id')->on('especialidades')->onDelete('cascade');
        });

        // 5. Crear tabla cita_tratamiento (cita - medicamentos)
        Schema::create('cita_tratamientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->string('dosis')->nullable();
            $table->string('duracion')->nullable();
            $table->text('indicaciones')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('medicamento_id')->references('id')->on('medicamentos')->onDelete('cascade');
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
