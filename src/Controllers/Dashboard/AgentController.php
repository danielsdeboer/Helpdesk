<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\Tickets;
use Aviator\Helpdesk\Traits\FetchesAuthorizedAgent;

class AgentController extends Controller
{
    use FetchesAuthorizedAgent;

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the controller.
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $agent = $this->fetchAuthorizedAgent();

        return view('helpdesk::dashboard.index')->with([
            'team' => Tickets::forAgent($agent)->team(),
            'overdue' => Tickets::forAgent($agent)->overdue(),
            'open' => Tickets::forAgent($agent)->all(),
            'collab' => Tickets::forAgent($agent)->collaborating(),
            'tab' => 'dashboard',
        ]);
    }
}
