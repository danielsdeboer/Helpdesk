<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;

class PermalinkController extends Controller
{
    /**
     * Display a instance of the resource.
     * @param Ticket $ticket
     * @return Reponse
     */
    public function show(Ticket $ticket, string $uuid)
    {
        $ticket = Ticket::where('uuid', $uuid)->first();

        if (! $ticket) {
            return redirect()->route('helpdesk.tickets.index');
        }

        return view('helpdesk::tickets.show')->with([
            'ticket' => $ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'showPrivate' => false,
        ]);
    }
}
