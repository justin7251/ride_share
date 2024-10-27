<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginVerificationNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Generate a random 6 digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save the code to the user model
        $notifiable->verification_code = $code;
        $notifiable->save();

        return (new MailMessage)
            ->line('Your login verification code is: ' . $code)
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, no further action is required.');
    }
}
