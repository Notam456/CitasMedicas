<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La reactivacion de un medico borraba la suspension, perdiendo el
     * historico. Con borrado logico el evento se conserva y las consultas
     * de vigencia lo ignoran automaticamente.
     */
    public function up(): void
    {
        Schema::table('suspensiones_medicos', function (Blueprint $table) {
            $table->softDeletes();
            $table->index(['medico_id', 'fecha_inicio', 'fecha_fin'], 'idx_suspensiones_medico_rango');
        });
    }

    public function down(): void
    {
        Schema::table('suspensiones_medicos', function (Blueprint $table) {
            $table->dropIndex('idx_suspensiones_medico_rango');
            $table->dropSoftDeletes();
        });
    }
};
