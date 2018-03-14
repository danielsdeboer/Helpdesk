<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class ClosedTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'closing'
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
            'closed' => $tickets->closed()->paginate(),
        ]);
    }
}
