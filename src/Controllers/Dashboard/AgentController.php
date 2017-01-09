<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\Tickets;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the controller
     * @return Response
     */
    public function index()
    {
        $agent = Agent::where('user_id', Auth::user()->id)->first();

        return view('helpdesk::dashboard.index')->with([
            'team' => Tickets::forAgent($agent)->team(),
            'overdue' => Tickets::forAgent($agent)->overdue(),
            'open' => Tickets::forAgent($agent)->all(),
            'tab' => 'dashboard',
        ]);
    }
}
