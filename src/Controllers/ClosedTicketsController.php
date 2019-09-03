<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;

class ClosedTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'user',
        'content',
        'closing',
        'closing.user',
        'closing.agent',
        'closing.agent.user',
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
        $closedTickets = null;

        if (in_array(auth()->user()->email, config('helpdesk.ignored'))) {
            $closedTickets = $tickets->with($this->relations)->closed()->paginate();
        } else {
            $closedTickets = $tickets->with($this->relations)->closedWithoutIgnored()->paginate();
        }

        return view('helpdesk::tickets.closed.index')->with([
            'closed' => $closedTickets,
        ]);
    }
}
