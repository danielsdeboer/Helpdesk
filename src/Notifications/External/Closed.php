<?php

namespace Aviator\Helpdesk\Notifications\External;

use Illuminate\Bus\Queueable;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Closed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ticket.
     * @var \Aviator\Helpdesk\Ticket
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
        $message->subject(config('helpdesk.notifications.external.closed.subject'));
        $message->greeting(config('helpdesk.notifications.external.closed.greeting'));
        $message->line(config('helpdesk.notifications.external.closed.line'));
        $message->action('View your ticket', route(config('helpdesk.notifications.external.closed.route'), $this->ticket->uuid));

        return $message;
    }
}
