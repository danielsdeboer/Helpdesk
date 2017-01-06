<?php

namespace Aviator\Helpdesk\Notifications\Internal;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedToAgent extends Notification implements ShouldQueue
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
        $message->subject(config('helpdesk.notifications.internal.assignedToUser.subject'));
        $message->greeting(config('helpdesk.notifications.internal.assignedToUser.greeting'));
        $message->line('You have been assigned a ticket from ' . $this->ticket->user->name);
        $message->action('View your ticket', route(config('helpdesk.notifications.internal.assignedToUser.route'), $this->ticket->id));

        return $message;
    }
}