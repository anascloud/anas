<?php

namespace App\Services;

use App\Models\TwoFactorCode;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorAuthenticator
{
    /**
     * Invalidate any pending codes, generate a fresh one, persist its hash,
     * and email the plain code to the user.
     */
    public function issueAndSend(User $user, ?string $ip = null): TwoFactorCode
    {
        // Invalidate any previous unconsumed codes for this user so only the
        // most recent one is ever valid.
        $user->twoFactorCodes()->whereNull('consumed_at')->update(['consumed_at' => now()]);

        $length = (int) config('two_factor.code_length');
        $plainCode = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        $record = $user->twoFactorCodes()->create([
            'code_hash' => Hash::make($plainCode),
            'attempts' => 0,
            'expires_at' => now()->addMinutes((int) config('two_factor.ttl_minutes')),
            'last_sent_at' => now(),
            'ip_address' => $ip,
        ]);

        $user->notify(new TwoFactorCodeNotification($plainCode, (int) config('two_factor.ttl_minutes')));

        return $record;
    }

    public function latestPendingCode(User $user): ?TwoFactorCode
    {
        return $user->twoFactorCodes()
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();
    }

    public function canResend(TwoFactorCode $code): bool
    {
        return $code->last_sent_at->addSeconds((int) config('two_factor.resend_cooldown_seconds'))->isPast();
    }

    public function secondsUntilResend(TwoFactorCode $code): int
    {
        return max(0, now()->diffInSeconds(
            $code->last_sent_at->addSeconds((int) config('two_factor.resend_cooldown_seconds')),
            false
        ));
    }

    /**
     * Verify a submitted code against the given pending record.
     *
     * @return array{ok: bool, reason: ?string}
     */
    public function attempt(TwoFactorCode $code, string $plainCode): array
    {
        if ($code->isConsumed()) {
            return ['ok' => false, 'reason' => 'no_pending_challenge'];
        }

        if ($code->isExpired()) {
            return ['ok' => false, 'reason' => 'code_expired'];
        }

        if ($code->maxAttemptsReached()) {
            return ['ok' => false, 'reason' => 'too_many_attempts'];
        }

        if (! $code->checkCode($plainCode)) {
            $code->increment('attempts');

            return ['ok' => false, 'reason' => $code->maxAttemptsReached() ? 'too_many_attempts' : 'code_invalid'];
        }

        $code->update(['consumed_at' => now()]);

        return ['ok' => true, 'reason' => null];
    }
}
