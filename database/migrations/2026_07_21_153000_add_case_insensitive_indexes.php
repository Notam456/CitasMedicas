<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'estados'       => ['columns' => 'LOWER(nombre)', 'unique' => true],
            'distritos'     => ['columns' => 'LOWER(nombre)', 'unique' => true],
            'especialidades' => ['columns' => 'LOWER(nombre)', 'unique' => true],
            'patologias'    => ['columns' => 'LOWER(nombre)', 'unique' => true],
            'municipios'    => ['columns' => 'LOWER(nombre), estado_id', 'unique' => true],
            'parroquias'    => ['columns' => 'LOWER(nombre), municipio_id', 'unique' => true],
        ];

        foreach ($indexes as $table => $cfg) {
            $uq = $cfg['unique'] ? 'UNIQUE' : '';
            $name = ($cfg['unique'] ? 'uq' : 'idx') . "_{$table}_nombre_ci";
            try {
                DB::statement("CREATE {$uq} INDEX {$name} ON {$table} ({$cfg['columns']})");
            } catch (\Exception $e) {
                // Si hay duplicados existentes, crear índice no único en su lugar
                DB::statement("CREATE INDEX IF NOT EXISTS idx_{$table}_nombre_ci ON {$table} ({$cfg['columns']})");
            }
        }
    }

    public function down(): void
    {
        $tables = ['estados', 'distritos', 'especialidades', 'patologias', 'municipios', 'parroquias'];
        foreach ($tables as $table) {
            DB::statement("DROP INDEX IF EXISTS uq_{$table}_nombre_ci");
            DB::statement("DROP INDEX IF EXISTS idx_{$table}_nombre_ci");
        }
    }
};
