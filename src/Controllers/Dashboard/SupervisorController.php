<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\View\View;
use Aviator\Helpdesk\Repositories\Tickets;
use Aviator\Helpdesk\Traits\FetchesAuthorizedAgent;

class SupervisorController extends Controller
{
    use FetchesAuthorizedAgent;

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('helpdesk.supervisors');
    }

    /**
     * Display an index of the controller.
     * @return View
     */
    public function index()
    {
        $super = $this->fetchAuthorizedAgent();

        return view('helpdesk::dashboard.index')->with([
            'unassigned' => Tickets::forSuper($super)->unassigned(),
            'overdue' => Tickets::forSuper($super)->overdue(),
            'open' => Tickets::forSuper($super)->all(),
            'collab' => Tickets::forSuper($super)->collaborating(),
            'tab' => 'dashboard',
            'isSuper' => true,
        ]);
    }
}
