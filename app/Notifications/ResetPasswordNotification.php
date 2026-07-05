<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url', config('app.url')).'/reset-password/'.$this->token
            .'?email='.urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject(__('mail.reset_password.subject'))
            ->greeting(__('mail.reset_password.greeting', ['name' => $notifiable->name]))
            ->line(__('mail.reset_password.intro'))
            ->action(__('mail.reset_password.action'), $url)
            ->line(__('mail.reset_password.expires', ['minutes' => (int) config('auth.passwords.users.expire')]))
            ->line(__('mail.reset_password.ignore'));
    }
}
