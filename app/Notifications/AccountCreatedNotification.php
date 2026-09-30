<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your InfraCon account has been created')
            ->greeting('Hello '.$notifiable->firstName.',')
            ->line('An InfraCon account has been created for you.')
            ->line('Your registered email address is '.$notifiable->email.'.')
            ->action('Sign in to InfraCon', route('login'))
            ->line('Use the initial password provided to you by your administrator. You can update your profile and password after signing in.');
    }
}
