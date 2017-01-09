<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;

class Tickets
{
    /**
     * The user
     * @var mixed User
     */
    protected $user;

    /**
     * The agent
     * @var Agent
     */
    protected $agent;

    /**
     * Constructor
     * @param User | Agent $user
     */
    public function __construct($userOrAgent)
    {
        $userModel = config('helpdesk.userModel');

        if ($user instanceof $userModel) {
            $this->user = $userOrAgent;
        } elseif ($user instanceof Agent) {
            $this->user = $userOrAgent->user;
            $this->agent = $userOrAgent;
        } else {
            throw new Exception('You must provide an instance of User or Agent');
        }
    }

    /**
     * Static constructor
     * @param  User | Agent $userOrAgent
     * @return Tickets
     */
    public function owner($userOrAgent)
    {
        return new self($userOrAgent);
    }

    /**
     * Return tickets assigned to the agent's team
     * @return Collection
     */
    public function team()
    {
        return Ticket::with('user', 'opening')
            ->whereHas('poolAssignment', function($query) {
                $query->whereIn('pool_id', $this->teamIds());
            })
            ->unassigned()
            ->get();
    }

    /**
     * Get the ids of this agent's teams
     * @return array
     */
    protected function teamIds()
    {
        return $this->agent->teams->pluck('id');
    }
}