<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Repositories\Tickets;
use Illuminate\Routing\Controller;

class SupervisorController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('helpdesk.supervisors');
    }

    /**
     * Display an index of the controller
     * @return Response
     */
    public function index()
    {
        $super = Agent::where('user_id', auth()->user()->id)->first();

        return [
            'unassigned' => Tickets::forSuper($super)->unassigned(),
            'overdue' => Tickets::forSuper($super)->overdue(),
            'super' => Tickets::forSuper($super)->all(),
        ];
    }
}
