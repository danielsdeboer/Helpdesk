<?php

namespace Aviator\Helpdesk\Queries;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

class TicketsQuery implements QueryInterface
{
    /**
     * The query builder.
     * @var Builder
     */
    protected $query;

    /**
     * Optional relations to eager load.
     * @var array
     */
    protected $relations = [];

    /**
     * Optional agent.
     * @var Agent
     */
    protected $agent;

    /**
     * The current user.
     * @var User
     */
    protected $user;

    /**
     * The tickets table from config.
     * @var string
     */
    protected $ticketsTable;

    /**
     * The due dates table from config.
     * @var string
     */
    protected $dueDatesTable;

    /**
     * Query selects.
     * @var array
     */
    protected $selects = [];

    //////////////////
    // Constructors //
    //////////////////

    /**
     * Constructor.
     * @param Model $model
     */
    public function __construct(Agent $agent = null)
    {
        $this->agent = $agent;
        $this->user = auth()->user();
        $this->query = Ticket::accessible($this->agent ?: $this->user);

        $this->ticketsTable = config('helpdesk.tables.tickets');
        $this->dueDatesTable = config('helpdesk.tables.due_dates');
        $this->usersTable = config('helpdesk.tables.tickets');
        $this->generateSelects();
    }

    /**
     * Static constructor.
     * @param  mixed[] ...$args
     * @return $this
     */
    public static function make(...$args)
    {
        return new self(...$args);
    }

    /**
     * Static actor - get the builder instance.
     * @param  mixed[]
     * @return Builder
     */
    public static function builder(...$args)
    {
        return (new self(...$args))->query();
    }

    ////////////////
    // Public Api //
    ////////////////

    /**
     * Return the builder instance.
     * @return Builder
     */
    public function query()
    {
        $this->build();

        return $this->query;
    }

    /**
     * Set the relations array.
     * @param array $relations
     */
    public function withRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Add an order by clause to the query builder, returning
     * results ordered by due soonest first.
     * @return $this
     */
    public function orderByDueSoonest()
    {
        $this->query->orderBy($this->dueDatesTable . '.due_on', 'asc');

        return $this;
    }

    /**
     * Get open tickets only.
     * @return $this
     */
    public function openOnly()
    {
        $this->query->opened();

        return $this;
    }

    /**
     * Get open tickets only.
     * @return $this
     */
    public function closedOnly()
    {
        $this->query->closed();

        return $this;
    }

    /**
     * Push a select onto the select stack.
     * @param string $select
     */
    public function addSelect($select)
    {
        $this->selects[] = $select;

        return $this;
    }

    //////////////////
    // Internal API //
    //////////////////

    protected function build()
    {
        $this->query
            ->select($this->selects)
            ->leftJoin(
                $this->dueDatesTable,
                $this->ticketsTable . '.id',
                $this->dueDatesTable . '.ticket_id'
            );
    }

    protected function generateSelects()
    {
        $this->selects = [
            $this->ticketsTable . '.id',
            'uuid',
            'user_id',
            'content_id',
            'content_type',
            'status',
            $this->ticketsTable . '.created_at',
            $this->ticketsTable . '.updated_at',
            $this->dueDatesTable . '.due_on',
        ];
    }
}
