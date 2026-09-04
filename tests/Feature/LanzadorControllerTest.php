<?php

namespace Tests\Feature;

use App\Models\LanzadorSesion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanzadorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cierra_la_sesion_vinculada_al_token(): void
    {
        $user = User::factory()->create();

        LanzadorSesion::create([
            'token' => 'tok-abc',
            'session_id' => 'sess-123',
            'user_id' => $user->id,
        ]);

        $this->post('/lanzador/cerrar-sesion', ['token' => 'tok-abc'])
            ->assertStatus(204);

        $this->assertDatabaseMissing('lanzador_sesiones', ['token' => 'tok-abc']);
        $this->assertDatabaseMissing('sessions', ['id' => 'sess-123']);
    }

    public function test_token_inexistente_responde_204(): void
    {
        $this->post('/lanzador/cerrar-sesion', ['token' => 'token-inexistente'])
            ->assertStatus(204);
    }
}
