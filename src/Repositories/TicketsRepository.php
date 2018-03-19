<?php

namespace Aviator\Helpdesk\Repositories;

use Illuminate\Support\Collection;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketsRepository extends Repository
{
    /** @var \Illuminate\Foundation\Auth\User */
    private $user;

    /** @var string */
    protected $orderByColumn = 'created_at';

    /** @var string */
    protected $orderByDirection = 'desc';

    /** @var array */
    protected $relations = [
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

        $this->query = $ticket::query();
        $this->user = $user;
        $this->scopeToUser();
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
