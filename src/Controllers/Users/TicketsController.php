<?php

namespace Aviator\Helpdesk\Controllers\Users;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
        $this->middleware('helpdesk.users');
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(TicketsRepository $tickets, Request $request, int $id)
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
