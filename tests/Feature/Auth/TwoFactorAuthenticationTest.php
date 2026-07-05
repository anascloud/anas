<?php

namespace Tests\Feature\Auth;

use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_requires_two_factor_code(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Password123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
    }

    public function test_login_with_invalid_password_fails(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Password123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_correct_code_completes_authentication(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'Password123']);

        $code = TwoFactorCode::where('user_id', $user->id)->latest('id')->first();
        // We can't know the plain code (only its hash is stored), so we fake
        // one directly for this test via the service, mirroring real behaviour.
        $plain = '123456';
        $code->update(['code_hash' => Hash::make($plain)]);

        $response = $this->post('/two-factor-challenge', ['code' => $plain]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_incorrect_code_is_rejected_and_counts_as_an_attempt(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'Password123']);

        $response = $this->post('/two-factor-challenge', ['code' => '000000']);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();

        $code = TwoFactorCode::where('user_id', $user->id)->latest('id')->first();
        $this->assertEquals(1, $code->attempts);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'Password123']);

        $code = TwoFactorCode::where('user_id', $user->id)->latest('id')->first();
        $plain = '123456';
        $code->update([
            'code_hash' => Hash::make($plain),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->post('/two-factor-challenge', ['code' => $plain]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_resend_is_blocked_during_cooldown(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'Password123']);

        $response = $this->post('/two-factor-challenge/resend');

        $response->assertSessionHasErrors('code');
    }
}
