<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * The agent
     * @var Agent
     */
    protected $agent;

    /**
     * The agents teams tickets
     * @var Collection
     */
    protected $teamTickets;

    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the controller
     * @return Response
     */
    public function index()
    {
        $this->agent = Agent::where('user_id', Auth::user()->id)->first();
        $this->getTeamTickets();

        return [
            'team' => $this->teamTickets,
            'teamCount' => $this->teamTickets->count(),

            'overdue' => Ticket::with('user', 'dueDate')->overdue()->get()->sortBy(function($item) {
                return $item->dueDate->due_on;
            }),

            'dueToday' => Ticket::with('user')->dueToday()->get(),

            'open' => Ticket::with('user')->opened()->get()->sortBy(function($item) {
                return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
            }),

            'headerTab' => 'dashboard',
        ];
    }

    /**
     * Get the tickets for the agents team
     * @return $this
     */
    protected function getTeamTickets()
    {
        $this->teamTickets = Ticket::with('user', 'opening')
            ->whereHas('poolAssignment', function($query) {
                $query->whereIn('pool_id', $this->agent->teams->pluck('id'));
            })
            ->unassigned()
            ->get();

        return $this;
    }
}
