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
    public function create(Ticket $ticket)
    {
        $collab = $this->fetchCollaborator();
        $creator = $this->fetchCreator();

        /*
         * Don't allow an agent to add themselves as a collaborator.
         */
        if ($collab->id === $creator->id) {
            return $this->redirect($ticket);
        }

        $ticket->addCollaborator($collab, $creator);

        return $this->redirect($ticket);
    }

    /**
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect(Ticket $ticket)
    {
        if (!$ticket) {
            return redirect()->route('helpdesk.tickets.index');
        }

        return redirect(
            route('helpdesk.tickets.show', $ticket->id)
        );
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function fetchCollaborator()
    {
        /** @var \Aviator\Helpdesk\Models\Agent $collab */
        $collab = Agent::query()->findOrFail(
            request('collab-id')
        );

        return $collab;
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function fetchCreator()
    {
        /** @var \Aviator\Helpdesk\Models\Agent $creator */
        $creator = Agent::query()->where('user_id', request()->user()->id)->first();

        return $creator;
    }
}
