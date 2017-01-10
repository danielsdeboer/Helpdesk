<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AssignmentController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('helpdesk.supervisors');
    }

    /**
     * Create a new assignment
     * @return Response
     */
    protected function create(Request $request, Ticket $ticket)
    {
        $agent = Agent::findOrFail($request->agent_id);

        $ticket->assignToAgent($agent);

        return redirect( route('helpdesk.tickets.show', $ticket->id) );
    }
}
