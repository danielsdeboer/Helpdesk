<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\AgentsRepository;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class TicketsController extends Controller
{
    /** @var array */
    protected $indexRelations = [
        'user',
        'content',
        'actions',
        'dueDate',
    ];

    /** @var array */
    protected $showRelations = [
        'actions',
    ];

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the resource.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     * @throws \InvalidArgumentException
     */
    public function index (TicketsRepository $tickets)
    {
        $openTickets = $tickets->clone()
            ->with($this->indexRelations)
            ->open()
            ->paginate();

        $closedTickets = $tickets->clone()
            ->with($this->indexRelations)
            ->paginate();

        return view('helpdesk::tickets.index')->with([
            'open' => $openTickets,
            'openCount' => $openTickets->total(),
            'closed' => $closedTickets,
            'closedCount' => $closedTickets->total(),
        ]);
    }

    /**
     * Display a instance of the resource.
     * @param AgentsRepository $agents
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show (AgentsRepository $agents, TicketsRepository $tickets, int $id)
    {
        $teamMembers = collect();

        $ticket = $tickets->with($this->showRelations)->findOrFail($id);

        if ($ticket->teamAssignment) {
            $agentsCollection = $agents->clone()->inTeam($ticket->teamAssignment->team)->get();
        } elseif (auth()->user()->is_super) {
            $agentsCollection = $agents->clone()->get();
        } elseif ($ticket->assignment) {
            foreach ($ticket->assignment->assignee->teamLeads as $key => $team) {
                $teamMembers->push($team->agents);
            }
            $agentsCollection = $teamMembers->flatten();
        } else {
            $agentsCollection = $agents->clone()->get();
        }

        return view('helpdesk::tickets.show')->with([
            'ticket' => $ticket,
            'agents' => $agentsCollection,
            'collaborators' => $agents->clone()->exceptAuthorized()->get(),
        ]);
    }
}
