<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Tickets
{
    /**
     * The user.
     * @var mixed
     */
    protected $user;

    /**
     * The agent.
     * @var \Aviator\Helpdesk\Models\Agent
     */
    protected $agent;

    /**
     * Is the agent a supervisor.
     * @var bool
     */
    protected $super;

    /*
     * Named Constructors
     */

    /**
     * Static constructor with agent.
     * @param  \Aviator\Helpdesk\Models\Agent $agent
     * @return \Aviator\Helpdesk\Repositories\Tickets
     */
    public static function forAgent($agent)
    {
        return (new self)->setAgent($agent);
    }

    /**
     * Static constructor with user.
     * @param mixed $user
     * @return \Aviator\Helpdesk\Repositories\Tickets
     */
    public static function forUser($user)
    {
        return (new self)->setUser($user);
    }

    /**
     * Static constructor with user.
     * @param  mixed $agent
     * @return \Aviator\Helpdesk\Repositories\Tickets
     */
    public static function forSuper(Agent $agent)
    {
        return (new self)->setAgent($agent)->setSuper($agent);
    }

    /*
     * Public Api
     */

    /**
     * Return tickets assigned to the agent's team.
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
    }

    /**
     * Return overdue tickets.
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
     * Get overdue tickets for a super.
     * @return Collection
     */
    protected function superOverdue()
    {
        return $this->ticketQuery()
            ->overdue()
            ->get()
            ->sortBy(function ($item) {
                return $item->dueDate->due_on;
            });
    }

    /**
     * Return all open tickets.
     * @return Collection
     */
    public function all()
    {
        if ($this->super) {
            return $this->ticketQuery()
                ->get()
                ->sortBy(function ($item) {
                    return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
                });
        }

        if ($this->agent) {
            return $this->ticketQuery()
                ->whereHas('assignment', function ($query) {
                    $query->where('assigned_to', $this->agent->id);
                })
                ->get()
                ->sortBy(function ($item) {
                    return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
                });
        }

        return $this->ticketQuery()
            ->where('user_id', $this->user->id)
            ->get();
    }

    /**
     * Get unassigned tickets.
     * @return Collection | null
     */
    public function unassigned()
    {
        if ($this->super) {
            return $this->ticketQuery()
                ->unassigned()
                ->get();
        }
    }

    /**
     * Get all tickets the agent is collaborating on.
     * @return \Illuminate\Support\Collection
     */
    public function collaborating()
    {
        return $this->ticketQuery()
            ->whereHas('collaborators', $this->collabCb())
            ->get();
    }

    /*
     * Callbacks
     */

    /**
     * Get the collaborator callback. Laravel wants a Closure here to we can't just use
     * this method as the callback. Instead we return a Closure.
     * @return \Closure
     */
    protected function collabCb()
    {
        return function (Builder $query) {
            $query->where('agent_id', $this->agent->id);
        };
    }

    /*
     * Helpers
     */

    /**
     * Get a base ticket query with the opened scope.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function ticketQuery()
    {
        return Ticket::with('user')
            ->opened();
    }

    /**
     * Get the ids of this agent's teams.
     * @return array
     */
    protected function teamIds()
    {
        return $this->agent->teams->pluck('id');
    }

    /**
     * Get overdue tickets for an agent.
     * @return Collection
     */
    protected function agentOverdue()
    {
        return Ticket::with('user', 'dueDate')
            ->whereHas('assignment', function ($query) {
                $query->where('assigned_to', $this->agent->id);
            })
            ->orWhereHas('teamAssignment', function ($query) {
                $query->whereIn('team_id', $this->teamIds());
            })
            ->overdue()
            ->get()
            ->sortBy(function ($item) {
                return $item->dueDate->due_on;
            });
    }

    /**
     * Get overdue tickets for a user.
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
     * Get tickets assigned to an agents teams.
     * @return Collection
     */
    protected function agentTeam()
    {
        return Ticket::with('user', 'opening')
            ->whereHas('teamAssignment', function ($query) {
                $query->whereIn('team_id', $this->teamIds());
            })
            ->opened()
            ->unassigned()
            ->get();
    }

    /**
     * Get tickets assigned to an agents teams.
     * @return Collection
     */
    protected function superTeam()
    {
        return Ticket::with('user', 'opening')
            ->whereHas('teamAssignment')
            ->opened()
            ->unassigned()
            ->get();
    }

    /*
     * Setters and Getters
     */

    /**
     * Set the agent.
     * @param Agent $agent
     * @return $this
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
        $this->setUser($agent->user);

        return $this;
    }

    /**
     * User getter.
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Super setter.
     * @param Agent $agent
     * @return $this
     */
    public function setSuper(Agent $agent)
    {
        if ($agent->isSuper()) {
            $this->super = true;
        }

        return $this;
    }

    /**
     * Super getter.
     * @return bool | null
     */
    public function getSuper()
    {
        return $this->super;
    }

    /**
     * Agent getter.
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set the user.
     * @param mixed $user
     * @return $this
     * @throws \Exception
     */
    public function setUser($user)
    {
        $userModel = config('helpdesk.userModel');

        if ($user instanceof $userModel) {
            $this->user = $user;
        } else {
            throw new \Exception('You must provide an instance of ' . $userModel);
        }

        return $this;
    }
}
