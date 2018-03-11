<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;

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
            'team' => $tickets->team()->unassigned()->get(),
            'overdue' => $tickets->overdue()->get(),
            'open' => $tickets->open()->get(),
            'collab' => $tickets->collaborating()->get(),
            'tab' => 'dashboard',
        ]);
    }
}
