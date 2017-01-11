<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NoteController extends Controller
{
    use ValidatesRequests;

    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'helpdesk.agents', 'helpdesk.ticket.assignee']);
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
        ], [
            'required' => 'The note body is required.'
        ]);

        $ticket->note($request->body, $agent);

        return redirect( route('helpdesk.tickets.show', $ticket->id) );
    }
}
