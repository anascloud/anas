<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('validation.custom.email.required'),
            'email.email' => __('validation.custom.email.email'),
        ]);

        // Always respond with the same generic status regardless of whether
        // the account exists, so the form can't be used to enumerate emails.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', __('auth.password_reset.sent'));
    }
}
