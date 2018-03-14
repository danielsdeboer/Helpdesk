<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class ClosedTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'user',
        'content',
        'closing',
        'closing.user',
        'closing.agent',
        'closing.agent.user'
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
        return view('helpdesk::tickets.closed.index')->with([
            'closed' => $tickets->with($this->relations)->closed()->paginate(),
        ]);
    }
}
