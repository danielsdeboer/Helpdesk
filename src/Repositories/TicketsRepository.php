<?php

namespace Aviator\Helpdesk\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Auth\User;

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

    /** @var bool */
    private $permalinkApplied = false;

    /**
     * Constructor.
     * @param \Aviator\Helpdesk\Models\Ticket $ticket
     * @param \Illuminate\Foundation\Auth\User $user
     */
    public function __construct (Ticket $ticket, User $user = null)
    {
        $this->query = $ticket::query();
        $this->user = $user;
        $this->addAutoScopes();
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
     * @param string $permalink
     * @return $this
     */
    public function permalink (string $permalink)
    {
        $this->permalinkApplied = true;

        return $this->addScope('permalink', $permalink);
    }

    /**
     * Pre-run query prepare. Can be over-ridden.
     * @return Builder
     */
    protected function prepare ()
    {
        if (!$this->user && !$this->permalinkApplied) {
            abort(403, 'Guests may only request tickets via permalinks.');
        }

        return $this->query();
    }

    /**
     * Apply query scopes based on whether the user is an agent or not.
     * @return $this
     */
    private function addAutoScopes ()
    {
        if ($this->user && $this->user->agent) {
            return $this->addScope('accessibleToAgent', $this->user->agent);
        }

        if ($this->user) {
            return $this->addScope('accessibleToUser', $this->user);
        }

        return $this;
    }
}
