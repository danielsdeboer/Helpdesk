<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClosingController extends Controller
{
    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'helpdesk.ticket.owner']);
    }

    /**
     * Create a new assignment.
     * @return Response
     */
    protected function create(Request $request, Ticket $ticket)
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        if ($agent) {
            $ticket->close($request->note, $agent);
        } else {
            $ticket->close($request->note, auth()->user());
        }

        return redirect(route('helpdesk.tickets.show', $ticket->id));
    }
}
