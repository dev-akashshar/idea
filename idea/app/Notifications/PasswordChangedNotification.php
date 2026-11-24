<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Changed - Idea App')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your account password was successfully updated.')
            ->line('If you made this change, no further action is required.')
            ->line('If you did NOT make this change, please contact support or reset your password immediately.');
    }
}
