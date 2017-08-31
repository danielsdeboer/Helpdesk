<?php

namespace Aviator\Helpdesk\Notifications\Internal;

use Illuminate\Bus\Queueable;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Collaborator extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ticket.
     * @var \Aviator\Helpdesk\Models\Ticket
     */
    public $ticket;

    /**
     * Create a new notification instance.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
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
        $message->subject(config('helpdesk.notifications.internal.collaborator.subject'));
        $message->greeting(config('helpdesk.notifications.internal.collaborator.greeting'));
        $message->line('You\'ve been added as a collaborator on a ticket from ' . $this->ticket->user->name);
        $message->action('View this ticket', route(
            config('helpdesk.notifications.internal.collaborator.route'),
            $this->ticket->id)
        );

        return $message;
    }
}
