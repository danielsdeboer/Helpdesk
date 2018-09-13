<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\EnabledAgentsRepository;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class PermalinkController extends Controller
{
    /** @var array */
    protected $showRelations = [
        'actions',
    ];

    /**
     * Display a instance of the resource.
     * @param EnabledAgentsRepository $agents
     * @param TicketsRepository $tickets
     * @param string $permalink
     * @return View
     */
    public function show(EnabledAgentsRepository $agents, TicketsRepository $tickets, string $permalink)
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
