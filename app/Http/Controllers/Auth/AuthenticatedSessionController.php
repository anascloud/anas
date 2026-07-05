<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Step 1 of login: verify email + password only. On success, stash the
     * user id in the session and send a 2FA code — the session is NOT yet
     * authenticated with Auth::login() until the code is verified.
     */
    public function store(LoginRequest $request, TwoFactorAuthenticator $twoFactor): RedirectResponse
    {
        $user = $request->authenticateCredentialsOnly();

        $request->session()->regenerate();
        $request->session()->put('2fa.user_id', $user->id);
        $request->session()->put('2fa.remember', $request->boolean('remember'));

        $twoFactor->issueAndSend($user, $request->ip());

        return redirect()->route('two-factor.challenge');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
