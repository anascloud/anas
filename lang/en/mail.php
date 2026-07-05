<?php

return [
    'two_factor' => [
        'subject' => 'Your QuantiTop verification code',
        'greeting' => 'Hi :name,',
        'intro' => 'Use the code below to finish signing in to QuantiTop.',
        'expires' => 'This code expires in :minutes minutes.',
        'ignore' => "If you didn't try to sign in, you can safely ignore this email.",
    ],
    'reset_password' => [
        'subject' => 'Reset your QuantiTop password',
        'greeting' => 'Hi :name,',
        'intro' => 'We received a request to reset the password for your account.',
        'action' => 'Reset Password',
        'expires' => 'This password reset link expires in :minutes minutes.',
        'ignore' => "If you didn't request a password reset, no further action is required.",
    ],
];
