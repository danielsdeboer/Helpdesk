<?php

namespace Aviator\Helpdesk\Queries;

use Aviator\Helpdesk\Queries\QueryInterface;
use Illuminate\Database\Eloquent\Builder;

class OpenTicketsQuery implements QueryInterface
{
    /**
     * The query builder
     * @var Builder
     */
    protected $query;

    /**
     * Optional relations to eager load
     * @var array
     */
    protected $relations = [];

    /**
     * Optional agent
     * @var Agent
     */
    protected $agent;

    //////////////////
    // Constructors //
    //////////////////

    /**
     * Constructor
     * @param Model $model
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Static constructor
     * @param  mixed[] ...$args
     * @return $this
     */
    public static function make(...$args)
    {
        return new self(...$args);
    }

    /**
     * Static actor - get the builder instance
     * @param  mixed[]
     * @return $this
     */
    public static function builder(...$args)
    {
        return (new self(...$args))->query();
    }

    ////////////////
    // Public Api //
    ////////////////

    /**
     * Return the builder instance
     * @return Builder
     */
    public function query()
    {
        $this->build();

        return $this->query;
    }

    /**
     * Set the relations array
     * @param array $relations
     */
    public function withRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Set the user or agent
     * @param  Agent $agent
     * @return $this
     */
    public function withAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    //////////////////
    // Internal API //
    //////////////////

    protected function build()
    {
        $this->query
            ->with($this->relations)
            ->accessible($this->agent ?: auth()->user())
            ->opened();
            // ->leftJoin(
            //     config('helpdesk.tables.due_dates'),
            //     $ticketTable . '.id',
            //     config('helpdesk.tables.due_dates') . '.ticket_id'
            // )
            // ->orderBy('due_on', 'asc');
    }
}
