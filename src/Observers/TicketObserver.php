<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Ticket;

class TicketObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        $this->createOpening($ticket);
    }

    protected function createOpening(Ticket $ticket)
    {
        Opening::create([
            'ticket_id' => $ticket->id,
            'agent_id' => $ticket->agent ? $ticket->agent->id : null,
            'user_id' => $ticket->user ? $ticket->user->id : null,
        ]);
    }
}