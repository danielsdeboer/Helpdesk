<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

class Status extends TicketHelper
{
    /**
     * Is the ticket open.
     * @return bool
     */
    public function isOpen () : bool
    {
        return $this->ticket->status === 'open';
    }
}
