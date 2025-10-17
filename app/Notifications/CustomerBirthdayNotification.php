<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Customer;

class CustomerBirthdayNotification extends Notification
{
    use Queueable;

    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function via($notifiable)
    {
        return ['database']; // Store notifications in the database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Today is {$this->customer->first_name} {$this->customer->last_name}'s birthday! 🎉",
            'customer_id' => $this->customer->id,
            'company_id' => $this->customer->company_id,
        ];
    }
}
