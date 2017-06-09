<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Opening;

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
            'is_visible' => true,
        ]);
    }
}
