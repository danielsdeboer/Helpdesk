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
     * NoteController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'helpdesk.ticket.owner']);
    }

    /**
     * Create a new assignment.
     * @param \Illuminate\Http\Request $request
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function create(Request $request, Ticket $ticket)
    {
        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

        $this->validate($request, [
            'note_body' => 'required|string',
        ], [
            'required' => 'The note body is required.',
        ]);

        $ticket->note(
            request('note_body'),
            $agent,
            (bool) request('note_is_visible')
        );

        return redirect(
            route('helpdesk.tickets.show', $ticket->id)
        );
    }
}
