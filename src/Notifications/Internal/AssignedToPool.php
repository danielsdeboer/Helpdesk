<?php

namespace Aviator\Helpdesk\Notifications\Internal;

use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignedToPool extends Notification
{
    use Queueable;

    /**
     * The ticket
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
        $message->subject(config('helpdesk.notifications.internal.assignedToPool.subject'));
        $message->greeting(config('helpdesk.notifications.internal.assignedToPool.greeting'));
        $message->line('A ticket from ' . $this->ticket->user->name . ' has been placed in your assignment pool. Press the button below to view the ticket and assign it to a user.');
        $message->action('View your ticket', route(config('helpdesk.notifications.internal.assignedToPool.route'), $this->ticket->id));

        return $message;
    }
}