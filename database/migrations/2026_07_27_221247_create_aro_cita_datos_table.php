<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aro_cita_datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id')->unique();
            $table->integer('semanas_gestacion')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aro_cita_datos');
    }
};
