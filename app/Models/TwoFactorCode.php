<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class TwoFactorCode extends Model
{
    protected $fillable = [
        'user_id',
        'code_hash',
        'attempts',
        'expires_at',
        'consumed_at',
        'last_sent_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'last_sent_at' => 'datetime',
        ];
    }

    public function belongsToUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function maxAttemptsReached(): bool
    {
        return $this->attempts >= (int) config('two_factor.max_attempts');
    }

    public function checkCode(string $plainCode): bool
    {
        return Hash::check($plainCode, $this->code_hash);
    }
}
