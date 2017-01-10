<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Routing\Controller;

class AssignmentController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('heldesk.supervisors');
    }

    /**
     * Create a new assignment
     * @return Response
     */
    protected function create()
    {
        // Code
    }
}
