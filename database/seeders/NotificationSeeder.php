<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\NuevaEspecialidad',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Nueva especialidad registrada',
                    'body' => 'Especialidad: Cardiología Pediátrica',
                    'action_url' => '/especialidades',
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ];

            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\NuevoMedico',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Nuevo médico incorporado',
                    'body' => 'Dr. Juan Pérez — Cardiología',
                    'action_url' => '/medicos',
                ]),
                'read_at' => Carbon::now()->subHour(),
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ];

            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\PlanificacionCreada',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Planificación de disponibilidad creada',
                    'body' => 'Dr. Juan Pérez — Cardiología: disponibilidad para 15 días.',
                    'action_url' => '/calendario',
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ];

            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\ResumenCitasDiario',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Resumen diario de citas',
                    'body' => 'Todas las citas del día fueron atendidas (12 en total).',
                    'action_url' => '/morbilidad/pendientes',
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ];
        }

        DB::table('notifications')->insert($notifications);
    }
}
