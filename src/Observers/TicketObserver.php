<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Action;
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
        $action = new Action;

        $action->name = 'Created';
        $action->subject_id = $ticket->id;
        $action->subject_type = Ticket::class;
        $action->save();
    }
}