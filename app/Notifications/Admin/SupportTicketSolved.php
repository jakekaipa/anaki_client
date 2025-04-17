<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketSolved extends Notification
{
    use Queueable;
    protected $support_ticket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($support_ticket)
    {
        $this->support_ticket = $support_ticket;
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
        $support_ticket = $this->support_ticket;
        return (new MailMessage)
                    ->greeting("Hello ".$support_ticket->name." !")
                    ->subject("Support ticket solved")
                    ->line('Your subject is'. $support_ticket->subject.', Your token is '.$support_ticket->token)
                    ->action('View Support Ticket', route('user.support.ticket.conversation',encrypt($support_ticket->id)))
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
