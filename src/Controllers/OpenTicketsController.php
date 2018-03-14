<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class OpenTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'content',
        'assignment.assignee.agent.user',
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
        return view('helpdesk::tickets.open.index')->with([
            'open' => $tickets->with($this->relations)->open()->paginate(),
        ]);
    }
}
