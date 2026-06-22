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
        Schema::dropIfExists('cita_referencias');
        Schema::dropIfExists('cita_tratamientos');

        Schema::create('cita_patologias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('patologia_id');
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('patologia_id')->references('id')->on('patologias')->onDelete('cascade');
            $table->unique(['cita_id', 'patologia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita_patologias');
    }
};
