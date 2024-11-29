<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ReauthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user is redirected to the reauthenticate form for protected routes.
     */
    public function test_user_is_redirected_to_reauthenticate_for_protected_route()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/settings');

        $response->assertRedirect(route('reauthenticate')); // Use route helper to ensure the route matches
    }

    /**
     * Test that a user can reauthenticate successfully.
     */
    public function test_user_can_reauthenticate_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->post('/reauthenticate', [
            'password' => 'password',
        ]);

        $this->assertEquals(
            'Reauthentication successful.',
            $response->getSession()->get('success')
        );

        $this->assertTrue(session()->has('reauthenticated_at'));
    }


    /**
     * Test that a user cannot reauthenticate with the wrong password.
     */
    public function test_user_cannot_reauthenticate_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->post('/reauthenticate', [
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect()
                 ->assertSessionHasErrors(['password']);
    }
}
