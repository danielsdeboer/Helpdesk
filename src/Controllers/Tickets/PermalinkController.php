<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\AgentsRepository;
use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PermalinkController extends Controller
{
    /** @var array */
    protected $showRelations = [
        'actions',
        'teamAssignment.team.agents',
        'opening.agent',
        'notes.agent',
        'dueDates.agent',
        'internalReplies.agent',
        'externalReplies.user',
        'closing.agent',
    ];

    /**
     * Display a instance of the resource.
     * @param AgentsRepository $agents
     * @param TicketsRepository $tickets
     * @param string $permalink
     * @return View
     */
    public function show(AgentsRepository $agents, TicketsRepository $tickets, string $permalink)
    {
        /** @var Ticket $ticket */
        $ticket = $tickets->with($this->showRelations)->permalink($permalink)->first();

        return view('helpdesk::tickets.show')->with([
            'ticket' => $ticket,
            'agents' => $ticket->teamAssignment
                ? $agents->clone()->inTeam($ticket->teamAssignment->team)->get()
                : $agents->clone()->get(),
            'collaborators' => $agents->clone()->exceptAuthorized()->get(),
        ]);
    }
}
