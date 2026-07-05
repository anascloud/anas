<?php

return [
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'two_factor' => [
        'code_sent' => 'A verification code has been sent to your email.',
        'code_invalid' => 'That code is incorrect. Please try again.',
        'code_expired' => 'This code has expired. Please request a new one.',
        'too_many_attempts' => 'Too many incorrect attempts. Please request a new code.',
        'resend_cooldown' => 'Please wait :seconds more seconds before requesting a new code.',
        'no_pending_challenge' => 'Your sign-in session has expired. Please log in again.',
    ],

    'registration' => [
        'email_taken' => 'An account with this email address already exists.',
    ],

    'password_reset' => [
        'sent' => 'If an account exists for that email, a password reset link has been sent.',
        'invalid_token' => 'This password reset link is invalid or has already been used.',
        'expired_token' => 'This password reset link has expired. Please request a new one.',
        'success' => 'Your password has been reset successfully. You can now log in.',
    ],
];
