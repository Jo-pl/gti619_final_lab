<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ActiveSession;

class ActiveSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_active_sessions()
    {
        $user = User::factory()->create();

        $session = ActiveSession::create([
            'user_id' => $user->id,
            'session_id' => 'session123',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'last_activity' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get('/active-sessions');

        $response->assertStatus(200)
                 ->assertViewHas('sessions', function ($sessions) use ($session) {
                     return $sessions->contains($session);
                 });
    }

    public function test_user_can_terminate_an_active_session()
    {
        $user = User::factory()->create();

        $session = ActiveSession::create([
            'user_id' => $user->id,
            'session_id' => 'session123',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'last_activity' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->delete('/active-sessions/' . $session->id);

        $response->assertRedirect('/active-sessions')
                 ->assertSessionHas('success', 'Session terminated successfully.');

        $this->assertDatabaseMissing('active_sessions', ['id' => $session->id]);
    }

    public function test_user_cannot_terminate_another_users_session()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $session = ActiveSession::create([
            'user_id' => $user2->id,
            'session_id' => 'session456',
            'ip_address' => '127.0.0.2',
            'user_agent' => 'Chrome/91',
            'last_activity' => now(),
        ]);

        $this->actingAs($user1);

        $response = $this->delete('/active-sessions/' . $session->id);

        $response->assertStatus(403);
    }
}
