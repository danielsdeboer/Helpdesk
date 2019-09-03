<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;

class OpenTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'content',
        'assignment.assignee.user',
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
        $openTickets = null;

        if (in_array(auth()->user()->email, config('helpdesk.ignored'))) {
            $openTickets = $tickets->with($this->relations)->open()->paginate();
        } else {
            $openTickets = $tickets->with($this->relations)->openWithoutIgnored()->paginate();
        }

        return view('helpdesk::tickets.open.index')->with([
            'open' => $openTickets,
        ]);
    }
}
