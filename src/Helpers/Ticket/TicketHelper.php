<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

use Aviator\Helpdesk\Models\Ticket;

abstract class TicketHelper
{
    /** @var \Aviator\Helpdesk\Models\Ticket */
    protected $ticket;

    /**
     * Constructor.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
