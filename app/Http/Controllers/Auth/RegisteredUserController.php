<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => [
                'required', 'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ], [
            'name.required' => __('validation.custom.name.required'),
            'name.min' => __('validation.custom.name.min'),
            'email.required' => __('validation.custom.email.required'),
            'email.email' => __('validation.custom.email.email'),
            'email.unique' => __('validation.custom.email.unique'),
            'password.required' => __('validation.custom.password.required'),
            'password.confirmed' => __('validation.custom.password.confirmed'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        // New registrations still go through the 2FA email challenge before
        // being treated as an authenticated session, for consistent security.
        $request->session()->put('2fa.user_id', $user->id);
        $request->session()->put('2fa.remember', false);

        app(\App\Services\TwoFactorAuthenticator::class)->issueAndSend($user, $request->ip());

        return redirect()->route('two-factor.challenge');
    }
}
