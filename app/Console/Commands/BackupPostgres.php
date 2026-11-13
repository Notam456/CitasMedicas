<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupPostgres extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Genera una copia de seguridad automática de PostgreSQL en Alwaysdata';

    public function handle()
    {
        $filename = 'backup_' . now()->format('Y_m_d_His') . '.dump';
        $disk = Storage::disk('local');
        $path = $disk->path('backups/' . $filename);
        
        if (!$disk->exists('backups')) {
            $disk->makeDirectory('backups');
        }

        $command = sprintf(
            'pg_dump -U %s -h %s -p %s -Fc -f "%s" %s',
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.host'),
            config('database.connections.pgsql.port'),
            $path,
            config('database.connections.pgsql.database')
        );

        $process = Process::fromShellCommandline($command, null, [
            'PGPASSWORD' => config('database.connections.pgsql.password')
        ]);

        $process->setTimeout(300); 
        $process->run();

        if ($process->isSuccessful()) {
            $this->info("Backup completado con éxito: {$filename}");
        } else {
            $this->error("Fallo al crear el backup: " . $process->getErrorOutput());
        }
    }
}