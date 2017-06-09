<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;

class NoteController extends Controller
{
    use ValidatesRequests;

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'helpdesk.agents', 'helpdesk.ticket.assignee']);
    }

    /**
     * Create a new assignment.
     * @return Response
     */
    protected function create(Request $request, Ticket $ticket)
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        $this->validate($request, [
            'note_body' => 'required|string',
        ], [
            'required' => 'The note body is required.',
        ]);

        $ticket->note($request->note_body, $agent, $request->note_is_visible ? true : false);

        return redirect(route('helpdesk.tickets.show', $ticket->id));
    }
}
