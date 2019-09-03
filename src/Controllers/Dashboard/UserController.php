<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the controller.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     */
    public function index(TicketsRepository $tickets)
    {
        return view('helpdesk::dashboard.index', [
            'open' => $tickets->clone()->open()->get(),
            'overdue' => $tickets->clone()->overdue()->get(),
            'tab' => 'dashboard',
        ]);
    }
}
