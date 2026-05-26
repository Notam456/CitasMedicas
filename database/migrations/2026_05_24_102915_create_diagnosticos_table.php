<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('patologia_id')->nullable()->constrained('patologias')->nullOnDelete();
            $table->text('diagnostico_libre')->nullable();
            $table->boolean('asistio')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // quien registra
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('diagnosticos'); }
};
