<?php

namespace Aviator\Helpdesk\Interfaces;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notification;

interface NotificationFactoryInterface
{
    public function make(string $classKey, Ticket $ticket): Notification;
}
