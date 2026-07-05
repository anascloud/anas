<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request, TwoFactorAuthenticator $twoFactor): Response|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $code = $twoFactor->latestPendingCode($user);

        return Inertia::render('Auth/TwoFactorChallenge', [
            'email' => $user->email,
            'canResendIn' => $code ? $twoFactor->secondsUntilResend($code) : 0,
        ]);
    }

    public function store(Request $request, TwoFactorAuthenticator $twoFactor): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:'.config('two_factor.code_length')],
        ], [
            'code.required' => __('validation.custom.code.required'),
            'code.digits' => __('validation.custom.code.digits'),
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            throw ValidationException::withMessages([
                'code' => __('auth.two_factor.no_pending_challenge'),
            ]);
        }

        $pending = $twoFactor->latestPendingCode($user);

        if (! $pending) {
            throw ValidationException::withMessages([
                'code' => __('auth.two_factor.no_pending_challenge'),
            ]);
        }

        $result = $twoFactor->attempt($pending, $request->input('code'));

        if (! $result['ok']) {
            throw ValidationException::withMessages([
                'code' => __('auth.two_factor.'.$result['reason']),
            ]);
        }

        $remember = (bool) $request->session()->pull('2fa.remember', false);
        $request->session()->forget('2fa.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function resend(Request $request, TwoFactorAuthenticator $twoFactor): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $pending = $twoFactor->latestPendingCode($user);

        if ($pending && ! $twoFactor->canResend($pending)) {
            throw ValidationException::withMessages([
                'code' => __('auth.two_factor.resend_cooldown', [
                    'seconds' => $twoFactor->secondsUntilResend($pending),
                ]),
            ]);
        }

        $twoFactor->issueAndSend($user, $request->ip());

        return back()->with('status', __('auth.two_factor.code_sent'));
    }

    private function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get('2fa.user_id');

        return $userId ? User::find($userId) : null;
    }
}
