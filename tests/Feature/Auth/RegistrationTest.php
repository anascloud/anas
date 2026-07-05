<?php

namespace Tests\Feature\Auth;

use App\Models\TwoFactorCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\TwoFactorCodeNotification;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register_and_are_sent_to_two_factor_challenge(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();

        $user = \App\Models\User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(1, TwoFactorCode::where('user_id', $user->id)->count());

        Notification::assertSentTo($user, TwoFactorCodeNotification::class);
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Different123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        \App\Models\User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
