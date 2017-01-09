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
     * Is the agent a supervisor
     * @var boolean | null
     */
    protected $super;

    ////////////////////////
    // NAMED CONSTRUCTORS //
    ////////////////////////

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
     * Static constructor with user
     * @param  mixed $agent
     * @return Tickets
     */
    public static function forSuper(Agent $agent)
    {
        return (new self)->setAgent($agent)->setSuper($agent);
    }

    ////////////////
    // PUBLIC API //
    ////////////////

    /**
     * Return tickets assigned to the agent's team
     * @return Collection
     */
    public function team()
    {
        if ($this->super) {
            return $this->superTeam();
        }

        if ($this->agent) {
            return $this->agentTeam();
        }

        return null;
    }

    /**
     * Return overdue tickets
     * @return Collection
     */
    public function overdue()
    {
        if ($this->super) {
            return $this->superOverdue();
        }

        if ($this->agent) {
            return $this->agentOverdue();
        }

        return $this->userOverdue();
    }

    /**
     * Get overdue tickets for a super
     * @return Collection
     */
    protected function superOverdue()
    {
        return Ticket::with('user')
            ->overdue()
            ->opened()
            ->get()
            ->sortBy(function($item) {
                return $item->dueDate->due_on;
            });
    }

    /**
     * Return all open tickets
     * @return Collection
     */
    public function all()
    {
        if ($this->super) {
            return Ticket::with('user')
                ->opened()
                ->get()
                ->sortBy(function($item) {
                    return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
                });
        }

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
     * Get unassigned tickets
     * @return Collection | null
     */
    public function unassigned()
    {
        if ($this->super) {
            return Ticket::with('user')
                ->opened()
                ->unassigned()
                ->get();
        }

        return null;
    }

    /////////////
    // HELPERS //
    /////////////

    /**
     * Get the ids of this agent's teams
     * @return array
     */
    protected function teamIds()
    {
        return $this->agent->teams->pluck('id');
    }

    /**
     * Get overdue tickets for an agent
     * @return Collection
     */
    protected function agentOverdue()
    {
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

    /**
     * Get overdue tickets for a user
     * @return Collection
     */
    protected function userOverdue()
    {
        return Ticket::with('user', 'dueDate')
            ->where('user_id', $this->user->id)
            ->overdue()
            ->get();
    }

    /**
     * Get tickets assigned to an agents teams
     * @return Collection
     */
    protected function agentTeam()
    {
        return Ticket::with('user', 'opening')
            ->whereHas('poolAssignment', function($query) {
                $query->whereIn('pool_id', $this->teamIds());
            })
            ->opened()
            ->unassigned()
            ->get();
    }

    /**
     * Get tickets assigned to an agents teams
     * @return Collection
     */
    protected function superTeam()
    {
        return Ticket::with('user', 'opening')
            ->whereHas('poolAssignment')
            ->opened()
            ->unassigned()
            ->get();
    }

    ///////////////////////
    // SETTERS & GETTERS //
    ///////////////////////

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
     * Super setter
     * @param Agent $agent
     */
    public function setSuper(Agent $agent)
    {
        if ($agent->user->{config('helpdesk.userModelEmailColumn')} == config('helpdesk.supervisor.email')) {
            $this->super = true;
        }

        return $this;
    }

    /**
     * Super getter
     * @return boolean | null
     */
    public function getSuper()
    {
        return $this->super;
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
}