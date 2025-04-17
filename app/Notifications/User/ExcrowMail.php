<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExcrowMail extends Notification
{
    use Queueable;

    public $user;
    public $data;
    public $trx_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $data, $trx_id)
    {
        $this->user = $user;
        $this->data = $data;
        $this->trx_id = $trx_id;
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
        $user = $this->user;
        $data = $this->data;
        $trx_id = $this->trx_id;
        $date = Carbon::now();
        $datetime = dateFormat('Y-m-d h:i:s A', $date);

        return (new MailMessage)
            ->greeting("Hello ".$user->fullname." !")
            ->subject("Trade created successful")
            ->line("Your purchased request send to admin successfully")
            ->line("Request Amount: " . getAmount($data['subtotal'],2).' '. $data['rate_currency_code'])
            ->line("Fees & Charges: " . $data['total_charge'].' '. $data['rate_currency_code'])
            ->line("Will Get: " . getAmount($data['rate'],2).' '. $data['rate_currency_code'])
            ->line("Total Payable Amount: " . getAmount($data['subtotal'] + $data['total_charge'],2).' '. $data['rate_currency_code'])
            ->line("Transaction Id: " .$trx_id)
            ->line("Status: Success")
            ->line("Date And Time: " .$datetime)
            ->line('Thank you for using our application!');
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
            //
        ];
    }
}
