<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Queries\TicketsQuery;
use Illuminate\Routing\Controller;

class TicketsController extends Controller
{
    /**
     * Who is the reponse for.
     * @var string
     */
    protected $for;

    /**
     * The ticket.
     * @var Ticket
     */
    protected $ticket;

    /**
     * Default relationships to get with the ticket.
     * @var array
     */
    protected $relations = [
        'assignment',
        'poolAssignment',
        'dueDate',
    ];

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

        $open = TicketsQuery::make()
            ->withAgent($agent)
            ->withRelations($this->relations)
            ->openOnly()
            ->orderByDueSoonest()
            ->query();

        $closed = TicketsQuery::make()
            ->withAgent($agent)
            ->withRelations($this->relations)
            ->closedOnly()
            ->orderByDueSoonest()
            ->query();

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

        $open = Ticket::with($this->relations)
            ->accessible($agent ? $agent : auth()->user())
            ->where('status', 'open');

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

        $closed = Ticket::with($this->relations)
            ->accessible($agent ? $agent : auth()->user())
            ->where('status', 'closed');

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

        $this->ticket = Ticket::with($this->relations)
            ->accessible($agent ? $agent : auth()->user())
            ->findOrFail($id);

        switch (true) {
            case ! $agent:
                return $this->showForUser();
            case $agent && $agent->user->$email == $supervisorEmail:
                return $this->showForSuper();
            case $agent && $this->ticket->poolAssignment && $agent->isMemberOf($this->ticket->poolAssignment->pool):
                return $this->showForTeamLead();
            default:
                return $this->showForAgent();
        }
    }

    /////////////////
    // Interal Api //
    /////////////////

    /**
     * Show a ticket for a user.
     * @return Response
     */
    protected function showForUser()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'user',
            'ticket' => $this->ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'showPrivate' => false,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for an agent.
     * @return Response
     */
    protected function showForAgent()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'ticket' => $this->ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for a superuser.
     * @return Response
     */
    protected function showForSuper()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'agents' => $this->getUsers()->toJson(),
            'ticket' => $this->ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'withAssign' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for a team lead.
     * @return Response
     */
    public function showForTeamLead()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'ticket' => $this->ticket,
            'agents' => $this->getUsers()->toJson(),
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'withAssign' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Get users based on context, sorted by name.
     * @return Collection
     */
    protected function getUsers()
    {
        if ($this->ticket->poolAssignment) {
            return $this->ticket->poolAssignment->pool->agents()
                ->with('user')
                ->get()
                ->sortBy('user.name');
        }

        return Agent::with('user')
            ->get()
            ->sortBy('user.name');
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
