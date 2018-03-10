<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

use Aviator\Helpdesk\Models\Ticket;

abstract class TicketHelper
{
    /** @var \Aviator\Helpdesk\Models\Ticket */
    protected $ticket;

    /**
     * Constructor.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     */
    public function __construct (Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
