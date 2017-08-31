<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;

class CollaboratorController extends Controller
{
    /**
     * CollaboratorController constructor.
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Create a new assignment.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function create(Ticket $ticket)
    {
        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        $agent = Agent::query()->findOrFail(
            request('collab_id')
        );

        $ticket->addCollaborator($agent);

        return redirect(route('helpdesk.tickets.show', $ticket->id));
    }
}
