<?php

namespace Aviator\Helpdesk\Notifications;

use Illuminate\Bus\Queueable;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Generic extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Ticket */
    private $ticket;

    /** @var string */
    private $name;

    /** @var string */
    private $address;

    /** @var string */
    private $subject;

    /** @var string */
    private $greeting;

    /** @var string */
    private $line;

    /** @var string */
    private $route;

    /** @var string */
    private $idType = 'uuid';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, array $params = [])
    {
        $this->ticket = $ticket;
        $this->setParams($params);
    }

    public function setParams(array $params)
    {
        foreach ($params as $field => $value) {
            $this->{$field} = $value;
        }

        return $this;
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

        $message->from($this->address, $this->name);
        $message->subject($this->subject);
        $message->greeting($this->greeting);
        $message->line($this->line);
        $message->action(
            'View your ticket',
            route($this->route, $this->ticket->{$this->idType})
        );

        return $message;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
