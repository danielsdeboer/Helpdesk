<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class AgentController extends Controller
{
    /**
     * Construct with agents only middleware.
     */
    public function __construct ()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the controller.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index (TicketsRepository $tickets)
    {
        return view('helpdesk::dashboard.index')->with([
            'team' => $tickets->clone()->team()->unassigned()->get(),
            'overdue' => $tickets->clone()->overdue()->get(),
            'open' => $tickets->clone()->openWithoutIgnored()->get(),
            'collab' => $tickets->clone()->collaborating()->get(),
            'tab' => 'dashboard',
        ]);
    }
}
