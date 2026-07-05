<?php

return [
    'custom' => [
        'name' => [
            'required' => 'Please enter your full name.',
            'min' => 'Your name must be at least :min characters.',
        ],
        'email' => [
            'required' => 'Please enter your email address.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'An account with this email address already exists.',
        ],
        'password' => [
            'required' => 'Please enter your password.',
            'min' => 'Your password must be at least :min characters.',
            'confirmed' => 'Password confirmation does not match.',
            'letters' => 'Your password must contain at least one letter.',
            'mixed' => 'Your password must contain both upper and lowercase letters.',
            'numbers' => 'Your password must contain at least one number.',
        ],
        'code' => [
            'required' => 'Please enter the verification code.',
            'size' => 'The code must be exactly :size digits.',
            'digits' => 'The code must contain only numbers.',
        ],
        'token' => [
            'required' => 'This reset link is missing required information.',
        ],
    ],
];
