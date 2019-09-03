<?php

namespace Aviator\Helpdesk\Controllers\Agents;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class TicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'assignment',
        'teamAssignment',
        'dueDate',
        'collaborators',
    ];

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the resource.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     */
    public function index (TicketsRepository $tickets)
    {
        $openTickets = $tickets->clone()
            ->with($this->relations)
            ->open();

        $closedTickets = $tickets->clone()
            ->with($this->relations)
            ->closed();

        return view('helpdesk::tickets.index')->with([
            'open' => $openTickets->paginate(),
            'openCount' => $openTickets->count(),
            'closed' => $closedTickets->paginate(),
            'closedCount' => $closedTickets->count(),
            'tab' => 'tickets',
        ]);
    }

    /**
     * Display a instance of the resource.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show (TicketsRepository $tickets, int $id)
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'user',
            'ticket' => $tickets->with($this->relations)->find($id),
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'showPrivate' => false,
            'tab' => 'tickets',
        ]);
    }
}
