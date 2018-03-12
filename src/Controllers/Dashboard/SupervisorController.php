<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class SupervisorController extends Controller
{
    /**
     * Construct with agents only middleware.
     */
    public function __construct ()
    {
        $this->middleware('helpdesk.supervisors');
    }

    /**
     * Display an index of the controller.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\View\View
     */
    public function index (TicketsRepository $tickets)
    {
        return view('helpdesk::dashboard.index')->with([
            'unassigned' => $tickets->unassigned()->get(),
            'overdue' => $tickets->overdue()->get(),
            'open' => $tickets->open()->get(),
            'collab' => $tickets->collaborating()->get(),
            'tab' => 'dashboard',
            'isSuper' => true,
        ]);
    }
}
