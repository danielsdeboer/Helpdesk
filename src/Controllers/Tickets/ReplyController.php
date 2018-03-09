<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ReplyController extends Controller
{
    use ValidatesRequests;

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

        $this->validate($request, [
            'reply_body' => 'required|string',
        ], [
            'required' => 'The reply body is required.',
        ]);

        if ($agent) {
            $ticket->internalReply($request->reply_body, $agent);
        } else {
            $ticket->externalReply($request->reply_body, auth()->user());
        }

        return redirect(route('helpdesk.tickets.show', $ticket->id));
    }
}
