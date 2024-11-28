<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangePasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_change_password_with_valid_data()
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->actingAs($user)
            ->post(route('password.change'), [
                'current_password' => 'OldPassword123!',
                'new_password' => 'NewPassword123!',
                'new_password_confirmation' => 'NewPassword123!',
            ])
            ->assertSessionHas('success', 'Your password has been changed successfully!');

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_change_password_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->actingAs($user)
            ->post(route('password.change'), [
                'current_password' => 'WrongPassword123!',
                'new_password' => 'NewPassword123!',
                'new_password_confirmation' => 'NewPassword123!',
            ])
            ->assertSessionHas('error', 'Your current password is incorrect.');

        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_reuse_recent_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        // Add password to history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->actingAs($user)
            ->post(route('password.change'), [
                'current_password' => 'OldPassword123!',
                'new_password' => 'OldPassword123!',
                'new_password_confirmation' => 'OldPassword123!',
            ])
            ->assertSessionHas('error', 'You cannot reuse your recent passwords.');

        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));
    }

    /** @test */
    public function password_must_meet_strength_requirements()
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->actingAs($user)
            ->post(route('password.change'), [
                'current_password' => 'OldPassword123!',
                'new_password' => 'weakpassword',
                'new_password_confirmation' => 'weakpassword',
            ])
            ->assertSessionHasErrors(['new_password']);

        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));
    }
}
