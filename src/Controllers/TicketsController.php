<?php

namespace Aviator\Helpdesk\Controllers;

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
            'ticket' => $tickets->with($this->relations)->findOrFail($id),
        ]);
    }
}
