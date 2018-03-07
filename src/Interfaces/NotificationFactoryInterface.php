<?php

namespace Aviator\Helpdesk\Interfaces;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;

interface NotificationFactoryInterface
{
    /**
     * @param string $classKey
     * @param Ticket $ticket
     * @return Notification
     */
    public function make (string $classKey, Ticket $ticket) : Notification;
}