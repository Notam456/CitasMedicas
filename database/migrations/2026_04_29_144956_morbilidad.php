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
    Schema::create('morbilidades', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cita_id')->unique()->constrained('citas')->cascadeOnDelete();
    $table->text('diagnostico');
    $table->text('observaciones')->nullable();
    $table->boolean('asistio')->default(false);
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
