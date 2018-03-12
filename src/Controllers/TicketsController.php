<?php

namespace Aviator\Helpdesk\Controllers;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Queries\TicketsQuery;

class TicketsController extends Controller
{
    /**
     * Who is the response for.
     * @var string
     */
    protected $for;

    /**
     * The ticket.
     * @var \Aviator\Helpdesk\Models\Ticket
     */
    protected $ticket;

    /**
     * Default relationships to get with the ticket.
     * @var array
     */
    protected $relations = [
        'assignment',
        'teamAssignment',
        'dueDate',
        'collaborators',
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
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     */
    public function index(TicketsRepository $tickets)
    {
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

        $open = $tickets->with($this->relations)
            ->open();
//            ->orderBy('dueDate', 'asc');
//        $open = TicketsQuery::make($agent)
//            ->withRelations($this->relations)
//            ->openOnly()
//            ->orderByDueSoonest()
//            ->query();

        $closed = TicketsQuery::make($agent)
            ->withRelations($this->relations)
            ->closedOnly()
            ->orderByDueOnDesc()
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
     * @return \Illuminate\Contracts\View\View
     */
    public function opened()
    {
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

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
     * @return \Illuminate\Contracts\View\View
     */
    public function closed()
    {
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

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
     * @param \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $id)
    {
        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        $agent = $request->user()->agent;

        $this->ticket = Ticket::with($this->relations)
            ->accessible($agent ?: $request->user())
            ->findOrFail($id);

        switch (true) {
            case ! $agent:
                return $this->showForUser();
            case $agent && $agent->isSuper():
                return $this->showForSuper();
            case $agent && $this->ticket->teamAssignment && $agent->isMemberOf($this->ticket->teamAssignment->team):
                return $this->showForTeamLead();
            case $agent && $this->ticket->status()->collaborates($agent):
                return $this->showForCollab();
            default:
                return $this->showForAgent();
        }
    }

    // Internal Api ----------------------------------------------------------------------------------------------------

    /**
     * Show a ticket for a user.
     * @return \Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\View\View
     */
    protected function showForAgent()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'ticket' => $this->ticket,
            'agents' => $this->getAssignmentAgents(),
            'collaborators' => $this->getCollaboratingAgents(),
            'agentsJson' => $this->getAssignmentAgents()->toJson(),
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'withCollab' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for a superuser.
     * @return \Illuminate\Contracts\View\View
     */
    protected function showForSuper()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'agents' => $this->getAssignmentAgents(),
            'agentsJson' => $this->getAssignmentAgents()->toJson(),
            'collaborators' => $this->getCollaboratingAgents(),
            'ticket' => $this->ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'withAssign' => true,
            'withCollab' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for a team lead.
     * @return \Illuminate\Contracts\View\View
     */
    public function showForTeamLead()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'ticket' => $this->ticket,
            'agents' => $this->getAssignmentAgents(),
            'agentsJson' => $this->getAssignmentAgents()->toJson(),
            'collaborators' => $this->getCollaboratingAgents(),
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
            'withNote' => true,
            'withAssign' => true,
            'withCollab' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Show a ticket for a team lead.
     * @return \Illuminate\Contracts\View\View
     */
    public function showForCollab()
    {
        return view('helpdesk::tickets.show')->with([
            'for' => 'agent',
            'ticket' => $this->ticket,
            'agents' => $this->getAssignmentAgents(),
            'agentsJson' => $this->getAssignmentAgents()->toJson(),
            'withReply' => true,
            'withNote' => true,
            'showPrivate' => true,
            'tab' => 'tickets',
        ]);
    }

    /**
     * Get users based on context, sorted by name.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAssignmentAgents ()
    {
        if ($this->ticket->teamAssignment) {
            return $this->ticket->teamAssignment->team->agents()
                ->with('user')
                ->get()
                ->sortBy('user.name');
        }

        return $this->getAllAgents();
    }

    /**
     * Get agents available for collaboration.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCollaboratingAgents ()
    {
        return $this->getAllAgents();
    }

    /**
     * Get all available agents.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAllAgents ()
    {
        return Agent::with('user')
            ->get()
            ->sortBy('user.name');
    }

    /**
     * Is the user allowed to access the ticket.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @return bool
     */
    protected function permitted(Ticket $ticket)
    {
        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = Ticket::query()
            ->find($ticket);

        $user = auth()->user();

        $agent = Agent::query()
            ->where('user_id', $user->id)
            ->first();

        $email = config('helpdesk.userModelEmailColumn');
        $supervisors = config('helpdesk.supervisors');

        if (in_array($user->$email, $supervisors)) {
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
