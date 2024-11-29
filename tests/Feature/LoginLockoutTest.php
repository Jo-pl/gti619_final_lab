<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_is_locked_after_max_failed_attempts()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $user->refresh(); // Refresh user instance to get updated data
    
        // Assert that the user is locked and the locked_until is set correctly
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'locked_until' => now()->addMinutes(15)->toDateTimeString(), // Use the same lockout time here
        ]);
    }

    
    public function test_locked_account_cannot_login()
    {
        Carbon::setTestNow(now());
    
        // Create a user with the locked_until field set to simulate a locked account
        $user = User::factory()->create([
            'locked_until' => now()->addMinutes(15),
        ]);
    
        // Attempt to log in with the locked account
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
    
        // Assert that the user is redirected back to login page
        $response->assertRedirect('/login');
        
        // Assert the correct session error message is in place
        $response->assertSessionHasErrors(['error' => ['Account locked. Try again in 15 minutes.']]);
    
        Carbon::setTestNow();
    }
    
    

    

}
