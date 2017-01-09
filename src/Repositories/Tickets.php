<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

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
     * Static constructor with agent
     * @param  Agent $agent
     * @return Tickets
     */
    public static function forAgent($agent)
    {
        return (new self)->setAgent($agent);
    }

    /**
     * Static constructor with user
     * @param  mixed $agent
     * @return Tickets
     */
    public static function forUser($user)
    {
        return (new self)->setUser($user);
    }

    /**
     * Set the agent
     * @param Agent $agent
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
        $this->setUser($agent->user);

        return $this;
    }

    /**
     * User getter
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Agent getter
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set the user
     * @param mixed $user
     */
    public function setUser($user)
    {
        $userModel = config('helpdesk.userModel');

        if ($user instanceof $userModel) {
            $this->user = $user;
        } else {
            throw new \Exception('You must provide an instance of '. $userModel);
        }

        return $this;
    }

    /**
     * Return tickets assigned to the agent's team
     * @return Collection
     */
    public function team()
    {
        if (! $this->agent) {
            return null;
        }

        return Ticket::with('user', 'opening')
            ->whereHas('poolAssignment', function($query) {
                $query->whereIn('pool_id', $this->teamIds());
            })
            ->unassigned()
            ->get();
    }

    /**
     * Return overdue tickets
     * @return Collection
     */
    public function overdue()
    {
        if ($this->agent) {
            return Ticket::with('user', 'dueDate')
                ->whereHas('assignment', function($query) {
                    $query->where('assigned_to', $this->agent->id);
                })
                ->orWhereHas('poolAssignment', function($query) {
                    $query->whereIn('pool_id', $this->teamIds());
                })
                ->overdue()
                ->get()
                ->sortBy(function($item) {
                    return $item->dueDate->due_on;
                });
        }

        return Ticket::with('user', 'dueDate')
            ->where('user_id', $this->user->id)
            ->overdue()
            ->get();
    }

    /**
     * Return all open tickets
     * @return Collection
     */
    public function all()
    {
        if ($this->agent) {
            return Ticket::with('user')
                ->whereHas('assignment', function($query) {
                    $query->where('assigned_to', $this->agent->id);
                })
                ->opened()
                ->get()
                ->sortBy(function($item) {
                    return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
                });
        }

        return Ticket::with('user')
            ->where('user_id', $this->user->id)
            ->opened()
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