<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use DB;
class PasswordReset extends Notification
{
    use Queueable;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
     /**
 * The password reset token.
 *
 * @var string
 */
public $token;

/**
 * Create a new notification instance.
 *
 * @return void
 */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {  
        
        $company = DB::table('theme_settings')->where('company_id', $notifiable->company_id)->pluck('site_name')->first();
     
        if($company==""){
            $company = 'B Saloon';
        }
        return (new MailMessage)
                    ->from('app@bSaloon.co.uk',$company)
                    ->greeting('Dear '.$notifiable->name)
                    ->line('We are sending this email because we recieved a forgot password request.')
                    ->action('Reset Password', url('password/reset', $this->token))
                    ->line('If you did not request a password reset, no further action is required. Please contact us if you did not submit this request.')
                    ->salutation($company);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //$notifiable->name
        ];
    }
}
