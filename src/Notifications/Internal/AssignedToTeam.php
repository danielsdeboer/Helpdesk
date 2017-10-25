<?php

namespace Aviator\Helpdesk\Notifications\Internal;

use Illuminate\Bus\Queueable;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssignedToTeam extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \Aviator\Helpdesk\Models\Ticket */
    public $ticket;

    /**
     * Constructor.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     */
    public function __construct (Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     * @param  mixed  $notifiable
     * @return array
     */
    public function via ($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail ($notifiable)
    {
        $message = new MailMessage;

        $message->from(config('helpdesk.from.address'), config('helpdesk.from.name'));
        $message->subject(config('helpdesk.notifications.internal.assignedToTeam.subject'));
        $message->greeting(config('helpdesk.notifications.internal.assignedToTeam.greeting'));
        $message->line('A ticket from ' . $this->ticket->user->name . ' has been placed in your assignment team. Press the button below to view the ticket and assign it to a user.');
        $message->action('View your ticket', route(config('helpdesk.notifications.internal.assignedToTeam.route'), $this->ticket->id));

        return $message;
    }
}
