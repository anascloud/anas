<?php

return [
    // Number of digits in the emailed one-time code.
    'code_length' => (int) env('TWO_FACTOR_CODE_LENGTH', 6),

    // How long a code stays valid after being issued.
    'ttl_minutes' => (int) env('TWO_FACTOR_CODE_TTL_MINUTES', 10),

    // Wrong-code attempts allowed before the code is locked and a new one must be requested.
    'max_attempts' => (int) env('TWO_FACTOR_MAX_ATTEMPTS', 5),

    // Minimum seconds between "resend code" requests, to prevent mail-bombing.
    'resend_cooldown_seconds' => (int) env('TWO_FACTOR_RESEND_COOLDOWN_SECONDS', 30),
];
