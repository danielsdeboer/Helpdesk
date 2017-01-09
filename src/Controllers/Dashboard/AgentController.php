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
     * Overdue tickets for this agent or team
     * @var Collection
     */
    protected $overdueTickets;

    /**
     * Open tickets assigned to agent
     * @var Collection
     */
    protected $agentTickets;

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
        $this->getTeamTickets()->getOverdueTickets()->getAgentTickets();

        return [
            'team' => $this->teamTickets,
            'teamCount' => $this->teamTickets->count(),

            'overdue' => $this->overdueTickets,
            'overdueCount' => $this->overdueTickets->count(),

            'agent' => $this->agentTickets,
            'agentCount' => $this->agentTickets->count(),

            'tab' => 'dashboard',
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

    /**
     * Get overdue tickets for this agent or agents team
     * @return $this
     */
    protected function getOverdueTickets()
    {
        $this->overdueTickets = Ticket::with('user', 'dueDate')
            ->whereHas('assignment', function($query) {
                $query->where('assigned_to', $this->agent->id);
            })
            ->orWhereHas('poolAssignment', function($query) {
                $query->whereIn('pool_id', $this->agent->teams->pluck('id'));
            })
            ->overdue()
            ->get()
            ->sortBy(function($item) {
                return $item->dueDate->due_on;
            });

        return $this;
    }

    /**
     * Get all open tickets assigned directly to the agent
     * @return $this
     */
    protected function getAgentTickets()
    {
        $this->agentTickets = Ticket::with('user')
            ->whereHas('assignment', function($query) {
                $query->where('assigned_to', $this->agent->id);
            })
            ->opened()
            ->get()
            ->sortBy(function($item) {
                return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
            });

        return $this;
    }
}
