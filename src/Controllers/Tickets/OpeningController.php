<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;

class OpeningController extends Controller
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
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Aviator\Helpdesk\Exceptions\CreatorRequiredException
     */
    protected function create(Request $request, Ticket $ticket)
    {
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($ticket->status()->closed()) {
            if ($agent) {
                $ticket->open($request->note, $agent);
            } else {
                $ticket->open($request->note, auth()->user());
            }
        }

        return redirect()->route('helpdesk.tickets.show', $ticket->id);
    }
}
