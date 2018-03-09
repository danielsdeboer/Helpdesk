<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;

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
        if (! $ticket) {
            return redirect()->route('helpdesk.tickets.index');
        }

        $agent = Agent::where('user_id', auth()->user()->id)->first();

        if ($agent) {
            $ticket->close($request->note, $agent);
        } else {
            $ticket->close($request->note, auth()->user());
        }

        return redirect(route('helpdesk.tickets.show', $ticket->id));
    }
}
