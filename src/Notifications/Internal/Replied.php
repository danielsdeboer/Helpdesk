<?php

namespace Aviator\Helpdesk\Notifications\Internal;

use Illuminate\Bus\Queueable;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Replied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ticket.
     * @var \Aviator\Helpdesk\Models\Ticket
     */
    public $ticket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
        $message = new MailMessage;

        $message->from(config('helpdesk.from.address'), config('helpdesk.from.name'));
        $message->subject(config('helpdesk.notifications.internal.replied.subject'));
        $message->greeting(config('helpdesk.notifications.internal.replied.greeting'));
        $message->line($this->ticket->user->name . ' has replied to your ticket. Press the button below to see the reply.');
        $message->action('View your ticket', route(config('helpdesk.notifications.internal.replied.route'), $this->ticket->id));

        return $message;
    }
}
