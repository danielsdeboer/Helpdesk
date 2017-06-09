<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;

class TicketsController extends Controller
{
    /**
     * Who is the reponse for.
     * @var string
     */
    protected $for;

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the resource.
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
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show an index of open tickets.
     * @return Response
     */
    public function opened()
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        $open = Ticket::accessible($agent ? $agent : auth()->user())->where('status', 'open');

        return view('helpdesk::tickets.opened')->with([
            'open' => $open->paginate(25),
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show an index of closed tickets.
     * @return Response
     */
    public function closed()
    {
        $agent = Agent::where('user_id', auth()->user()->id)->first();

        $closed = Ticket::accessible($agent ? $agent : auth()->user())->where('status', 'closed');

        return view('helpdesk::tickets.closed')->with([
            'closed' => $closed->paginate(25),
            'tab' => 'tickets',
        ]);
    }

    /**
     * Display a instance of the resource.
     * @param  int $id
     * @return Reponse
     */
    public function show($id)
    {
        $supervisorEmail = config('helpdesk.supervisor.email');
        $email = config('helpdesk.userModelEmailColumn');

        $agent = Agent::where('user_id', auth()->user()->id)->first();

        if (! $agent) {
            $for = 'user';
        } elseif ($agent && $agent->user->$email == $supervisorEmail) {
            $for = 'super';
        } else {
            $for = 'agent';
        }

        $ticket = Ticket::accessible($agent ? $agent : auth()->user())->findOrFail($id);

        if ($for === 'user') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'user',
                'ticket' => $ticket,
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'showPrivate' => false,
                'tab' => 'tickets',
            ]);
        }

        if ($for === 'agent') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'agent',
                'ticket' => $ticket,
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'withNote' => true,
                'showPrivate' => true,
                'tab' => 'tickets',
            ]);
        }

        if ($for === 'super') {
            return view('helpdesk::tickets.show')->with([
                'for' => 'agent',
                'ticket' => $ticket,
                'agents' => Agent::with('user')->get()->toJson(),
                'withOpen' => true,
                'withClose' => true,
                'withReply' => true,
                'withNote' => true,
                'withAssign' => true,
                'showPrivate' => true,
                'tab' => 'tickets',
            ]);
        }
    }

    /**
     * Is the user allowed to access the ticket.
     * @return bool
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
