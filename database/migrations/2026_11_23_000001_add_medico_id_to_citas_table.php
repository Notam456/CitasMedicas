<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('medico_id')
                ->nullable()
                ->after('calendario_id')
                ->constrained('medicos')
                ->restrictOnDelete();
            $table->index('medico_id');
        });

        DB::table('citas')
            ->whereNotNull('calendario_id')
            ->update([
                'medico_id' => DB::raw('(SELECT calendarios.medico_id FROM calendarios WHERE calendarios.id = citas.calendario_id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['medico_id']);
            $table->dropIndex(['medico_id']);
            $table->dropColumn('medico_id');
        });
    }
};
