<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;

class TicketObserver
{
    /** @var Opening */
    private $opening;

    /**
     * Constructor.
     * @param Opening $opening
     */
    public function __construct (Opening $opening)
    {
        $this->opening = $opening;
    }

    /**
     * Listen to the creating event.
     * @param Ticket $ticket
     * @return void
     */
    public function creating(Ticket $ticket)
    {
        // Check if user's email is on blacklist.
        if (isset($ticket->user->email)) {
            if (in_array($ticket->user->email, config('helpdesk.ignored'))) {
                $ticket->is_ignored = Carbon::now()->toDateTimeString();
            }
        }
    }

    /**
     * Listen to the created event.
     * @param Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        $this->createOpening($ticket);
    }

    /**
     * @param Ticket $ticket
     */
    protected function createOpening (Ticket $ticket)
    {
        $this->opening->create([
            'ticket_id' => $ticket->id,
            'agent_id' => $ticket->agent ? $ticket->agent->id : null,
            'user_id' => $ticket->user ? $ticket->user->id : null,
            'is_visible' => true,
        ]);
    }
}
