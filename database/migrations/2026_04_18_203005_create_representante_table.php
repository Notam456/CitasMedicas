<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representante', function (Blueprint $table) {
            $table->id('id_representante');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('cedula', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('parentesco', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representante');
    }
};
