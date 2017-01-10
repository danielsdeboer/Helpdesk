<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReplyController extends Controller
{
    use ValidatesRequests;

    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'helpdesk.ticket.owner']);
    }

    /**
     * Create a new assignment
     * @return Response
     */
    protected function create(Request $request, Ticket $ticket)
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        $this->validate($request, [
            'body' => 'required|string',
        ]);

        if ($agent) {
            $ticket->internalReply($request->body, $agent);
        } else {
            $ticket->externalReply($request->body, auth()->user());
        }

        return redirect( route('helpdesk.tickets.show', $ticket->id) );
    }
}
