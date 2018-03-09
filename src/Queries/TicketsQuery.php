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
     * @var mixed
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
     * TicketsQuery constructor.
     * @param Agent|null $agent
     */
    public function __construct (Agent $agent = null)
    {
        $this->agent = $agent;
        $this->user = auth()->user();
        $this->query = Ticket::accessible($this->agent ?: $this->user);

        $this->ticketsTable = config('helpdesk.tables.tickets');
        $this->dueDatesTable = config('helpdesk.tables.due_dates');
        $this->generateSelects();
    }

    /**
     * Static constructor.
     * @param  mixed[] ...$args
     * @return $this
     */
    public static function make (...$args)
    {
        return new self(...$args);
    }

    /**
     * Static actor - get the builder instance.
     * @param  mixed[]
     * @return Builder
     */
    public static function builder (...$args) : Builder
    {
        return self::make(...$args)->query();
    }

    /*
     * Public Api
     */

    /**
     * Return the builder instance.
     * @return Builder
     */
    public function query () : Builder
    {
        $this->build();

        return $this->query;
    }

    /**
     * Set the relations array.
     * @param array $relations
     * @return TicketsQuery
     */
    public function withRelations (array $relations) : TicketsQuery
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Add an order by clause to the query builder, returning
     * results ordered by due soonest first.
     * @return TicketsQuery
     */
    public function orderByDueSoonest () : TicketsQuery
    {
        $this->query->orderBy($this->dueDatesTable . '.due_on', 'asc');

        return $this;
    }

    /**
     * Order by due date descending, eg latest first.
     * @return TicketsQuery
     */
    public function orderByDueOnDesc () : TicketsQuery
    {
        $this->query->orderBy($this->dueDatesTable . '.due_on', 'desc');

        return $this;
    }

    /**
     * Get open tickets only.
     * @return TicketsQuery
     */
    public function openOnly () : TicketsQuery
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->query->opened();

        return $this;
    }

    /**
     * Get open tickets only.
     * @return TicketsQuery
     */
    public function closedOnly () : TicketsQuery
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->query->closed();

        return $this;
    }

    /**
     * Push a select onto the select stack.
     * @param string $select
     * @return TicketsQuery
     */
    public function addSelect (string $select) : TicketsQuery
    {
        $this->selects[] = $select;

        return $this;
    }

    /*
     * Internal Api
     */

    /**
     * Build the query base.
     * @return void
     */
    protected function build ()
    {
        $this->query
            ->select($this->selects)
            ->leftJoin(
                $this->dueDatesTable,
                $this->ticketsTable . '.id',
                $this->dueDatesTable . '.ticket_id'
            );
    }

    /**
     * Generate the selects to use for this query.
     * @return void
     */
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
