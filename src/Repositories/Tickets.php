<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Tickets
{
    /** @var Model */
    protected $user;

    /** @var Agent */
    protected $agent;

    /** @var bool */
    protected $super;

    /*
     * Named Constructors
     */

    /**
     * Static constructor with agent.
     * @param  \Aviator\Helpdesk\Models\Agent $agent
     * @return \Aviator\Helpdesk\Repositories\Tickets
     * @throws Exception
     */
    public static function forAgent ($agent) : Tickets
    {
        return (new self)->setAgent($agent);
    }

    /**
     * Static constructor with user.
     * @param mixed $user
     * @return \Aviator\Helpdesk\Repositories\Tickets
     * @throws Exception
     */
    public static function forUser ($user) : Tickets
    {
        return (new self)->setUser($user);
    }

    /**
     * Static constructor with user.
     * @param  mixed $agent
     * @return \Aviator\Helpdesk\Repositories\Tickets
     * @throws Exception
     */
    public static function forSuper (Agent $agent) : Tickets
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
    public function team () : Collection
    {
        if ($this->super) {
            return $this->superTeam();
        }

        if ($this->agent) {
            return $this->agentTeam();
        }

        return new Collection;
    }

    /**
     * Return overdue tickets.
     * @return Collection
     */
    public function overdue () : Collection
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
    protected function superOverdue() : Collection
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
    public function all () : Collection
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
                ->whereHas('assignment', function (Builder $query) {
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
     * @return Collection
     */
    public function unassigned () : Collection
    {
        if ($this->super) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->ticketQuery()
                ->unassigned()
                ->get();
        }

        return new Collection;
    }

    /**
     * Get all tickets the agent is collaborating on.
     * @return Collection
     */
    public function collaborating () : Collection
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
     * @return Closure
     */
    protected function collabCb () : Closure
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
    protected function ticketQuery() : Builder
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Ticket::with('user')
            ->opened();
    }

    /**
     * Get the ids of this agent's teams.
     * @return Collection
     */
    protected function teamIds () : Collection
    {
        return $this->agent->teams->pluck('id');
    }

    /**
     * Get overdue tickets for an agent.
     * @return Collection
     */
    protected function agentOverdue () : Collection
    {
        /** @noinspection PhpStaticAsDynamicMethodCallInspection */
        return Ticket::with('user', 'dueDate')
            ->whereHas('assignment', function (Builder $query) {
                $query->where('assigned_to', $this->agent->id);
            })
            ->orWhereHas('teamAssignment', function (Builder $query) {
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
    protected function userOverdue () : Collection
    {
        /** @noinspection PhpStaticAsDynamicMethodCallInspection */
        return Ticket::with('user', 'dueDate')
            ->where('user_id', $this->user->id)
            ->overdue()
            ->get();
    }

    /**
     * Get tickets assigned to an agents teams.
     * @return Collection
     */
    protected function agentTeam () : Collection
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Ticket::with('user', 'opening')
            ->whereHas('teamAssignment', function (Builder $query) {
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
    protected function superTeam () : Collection
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
     * @return Tickets
     * @throws Exception
     */
    public function setAgent (Agent $agent) : Tickets
    {
        $this->agent = $agent;
        $this->setUser($agent->user);

        return $this;
    }

    /**
     * User getter.
     * @return mixed
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * Super setter.
     * @param Agent $agent
     * @return Tickets
     */
    public function setSuper (Agent $agent) : Tickets
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
    public function getAgent () : Agent
    {
        return $this->agent;
    }

    /**
     * Set the user.
     * @param mixed $user
     * @return Tickets
     * @throws Exception
     */
    public function setUser($user) : Tickets
    {
        $userModel = config('helpdesk.userModel');

        if ($user instanceof $userModel) {
            $this->user = $user;
        } else {
            throw new Exception('You must provide an instance of ' . $userModel);
        }

        return $this;
    }
}
