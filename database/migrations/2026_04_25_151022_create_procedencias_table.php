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
        Schema::create('procedencias', function (Blueprint $table) {
            $table->id();
            
            // Relaciones con las tablas que ya creamos
            // foreignId crea la columna y constrained() le dice a Laravel con qué tabla se conecta
            $table->foreignId('estado_id')->constrained('estados')->onDelete('cascade');
            $table->foreignId('municipio_id')->constrained('municipios')->onDelete('cascade');
            $table->foreignId('parroquia_id')->constrained('parroquias')->onDelete('cascade');
            
            $table->timestamps();
        });
     }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedencias');
    }
};
