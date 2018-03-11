<?php

namespace Aviator\Helpdesk\Repositories;

use Illuminate\Support\Collection;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Auth\User;

class TicketsRepository
{
    /** @var \Aviator\Helpdesk\Models\Ticket */
    private $query;

    /** @var \Illuminate\Foundation\Auth\User */
    private $user;

    /** @var string */
    private $orderByColumn = 'created_at';

    /** @var string */
    private $orderByDirection = 'desc';

    /** @var array */
    private $relations = [
        'user',
        'dueDate',
        'opening',
    ];

    /**
     * Constructor.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @param \Illuminate\Foundation\Auth\User $user
     */
    public function __construct (Ticket $ticket, User $user = null)
    {
        if (! $user) {
            abort(430);
        }

        $this->query = $ticket->query();
        $this->user = $user;
        $this->scopeToUser();
    }

    /**
     * Get a single ticket by id.
     * @param int $id
     * @return Ticket|null
     */
    public function find (int $id)
    {
        return $this->query
            ->with($this->relations)
            ->find($id);
    }

    /**
     * Get a collection of tickets.
     * @return \Illuminate\Support\Collection
     */
    public function get () : Collection
    {
        return $this->query
            ->with($this->relations)
            ->orderBy($this->orderByColumn, $this->orderByDirection)
            ->get();
    }

    /**
     * @return $this
     */
    public function closed ()
    {
        return $this->addScope('closed');
    }

    /**
     * @return $this
     */
    public function collaborating ()
    {
        if ($this->user->agent) {
            return $this->addScope('collaborating', $this->user->agent);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function open ()
    {
        return $this->addScope('opened');
    }

    /**
     * @return $this
     */
    public function overdue ()
    {
        return $this->addScope('overdue');
    }

    /**
     * @return $this
     */
    public function team ()
    {
        if ($this->user->agent) {
            return $this->addScope('team', $this->user->agent);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function unassigned ()
    {
        return $this->addScope('unassigned');
    }

    /**
     * @param string $name
     * @param $arguments
     * @return $this
     */
    private function addScope (string $name, $arguments = []) : self
    {
        $this->query->scopes([
            $name => is_array($arguments) ? $arguments : [$arguments],
        ]);

        return $this;
    }

    /**
     * Apply query scopes based on whether the user is an agent or not.
     * @return $this
     */
    private function scopeToUser () : self
    {
        if ($this->user->agent) {
            return $this->addScope('accessibleToAgent', $this->user->agent);
        }

        return $this->addScope('accessibleToUser', $this->user);
    }
}
