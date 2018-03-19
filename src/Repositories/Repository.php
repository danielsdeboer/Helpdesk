<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Collection;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class Repository
{
    /** @var \Illuminate\Database\Eloquent\Builder */
    protected $query;

    /** @var string */
    protected $orderByColumn = 'created_at';

    /** @var string */
    protected $orderByDirection = 'desc';

    /** @var array */
    protected $relations = [];

    /** @var int */
    protected $resultsPerPage = 24;

    /**
     */
    public function clone ()
    {
        return app(self::class);
    }

    /**
     * Get a count of the result set.
     * @return int
     */
    public function count () : int
    {
        return $this->query->count();
    }

    /**
     * Get a single ticket by id.
     * @param int $id
     * @return Ticket|null
     */
    public function find (int $id)
    {
        return $this->query()
            ->find($id);
    }

    /**
     * @param int $id
     * @return Ticket|null
     */
    public function findOrFail (int $id)
    {
        return $this->query()
            ->findOrFail($id);
    }

    /**
     * Get a collection of results.
     * @return \Illuminate\Support\Collection
     */
    public function get () : Collection
    {
        return $this->query()
            ->orderBy($this->orderByColumn, $this->orderByDirection)
            ->get();
    }

    /**
     * Get a paginated collection of results.
     * @param int $resultsPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \InvalidArgumentException
     */
    public function paginate (int $resultsPerPage = null) : LengthAwarePaginator
    {
        return $this->query()
            ->orderBy($this->orderByColumn, $this->orderByDirection)
            ->paginate($resultsPerPage ?: $this->resultsPerPage);
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function with (array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param string $name
     * @param $arguments
     * @return $this
     */
    protected function addScope (string $name, $arguments = []) : self
    {
        $this->query->scopes([
            $name => is_array($arguments) ? $arguments : [$arguments],
        ]);

        return $this;
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Builder|static
     */
    protected function query ()
    {
        return $this->query->with($this->relations);
    }
}
