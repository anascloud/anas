<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $plainCode,
        private readonly int $ttlMinutes,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.two_factor.subject'))
            ->greeting(__('mail.two_factor.greeting', ['name' => $notifiable->name]))
            ->line(__('mail.two_factor.intro'))
            ->line(new \Illuminate\Support\HtmlString(
                '<div style="font-size:32px;font-weight:700;letter-spacing:8px;text-align:center;'
                .'margin:24px 0;color:#2563eb;">'.$this->plainCode.'</div>'
            ))
            ->line(__('mail.two_factor.expires', ['minutes' => $this->ttlMinutes]))
            ->line(__('mail.two_factor.ignore'));
    }
}
