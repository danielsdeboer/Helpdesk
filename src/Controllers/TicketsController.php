<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Routing\Controller;

class TicketsController extends Controller
{
    /**
     * Who is the reponse for
     * @var string
     */
    protected $for;

    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the resource
     * @return Response
     */
    public function index()
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        $open = Ticket::accessible($agent ? $agent : auth()->user())->where('status', 'open');
        $closed = Ticket::accessible($agent ? $agent : auth()->user())->where('status', 'closed');

        return view('helpdesk::tickets.index')->with([
            'open' => $open->paginate(25),
            'openCount' => $open->count(),
            'closed' => $closed->paginate(25),
            'closedCount' => $closed->count(),
        ]);
    }

    /**
     * Display a instance of the resource
     * @param  Ticket $ticket
     * @return Reponse
     */
    public function show($ticket)
    {
        if (! $this->permitted($ticket)) {
            abort(403, 'You are not permitted to access this resource.');
        }

        if ($this->for === 'user') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'user',
                'ticket' => Ticket::with('actions')->find($ticket),
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'showPrivate' => false,
            ]);
        }

        if ($this->for === 'agent') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'agent',
                'ticket' => Ticket::with('actions')->find($ticket),
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'withNote' => true,
                'showPrivate' => true,
            ]);
        }

        if ($this->for === 'super') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'agent',
                'ticket' => Ticket::with('actions')->find($ticket),
                'agents' => Agent::with('user')->get()->toJson(),
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'withNote' => true,
                'withAssign' => true,
                'showPrivate' => true,
            ]);
        }
    }

    /**
     * Is the user allowed to access the ticket
     * @return boolean
     */
    protected function permitted($ticket)
    {
        $ticket = Ticket::find($ticket);
        $user = auth()->user();
        $agent = Agent::where('user_id', $user->id)->first();
        $email = config('helpdesk.userModelEmailColumn');

        if ($user->$email == config('helpdesk.supervisor.email')) {
            $this->for = 'super';
            return true;
        }

        if ($agent && $ticket->assignment && $ticket->assignment->assignee->id == $agent->id) {
            $this->for = 'agent';
            return true;
        }

        if (! $agent && $ticket->user->id == $user->id) {
            $this->for = 'user';
            return true;
        }

        return false;
    }
}
