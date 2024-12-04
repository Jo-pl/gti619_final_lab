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
    
        for ($i = 0; $i < $this->maxAttempts; $i++) { // Adjust to match your maxAttempts
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }
    
        $user->refresh(); // Refresh user instance to get updated data
    
        $this->assertNotNull($user->locked_until);
        $this->assertTrue(Carbon::now()->lessThan($user->locked_until));
    }
    
    public function test_locked_account_cannot_login()
    {
        Carbon::setTestNow(now());
    
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'locked_until' => now()->addMinutes(15),
        ]);
    
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['error' => 'Account locked. Try again in 15 minutes.']);
    
        Carbon::setTestNow();
    }
    
    

    

}
