<?php

namespace Aviator\Helpdesk\Repositories;

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
     * Get a fresh instance of the repository.
     * @return static
     */
    public function clone ()
    {
        return app(static::class);
    }

    /**
     * Get a count of the result set.
     * @return int
     */
    public function count () : int
    {
        return $this->prepare()->count();
    }

    /**
     * Get a single ticket by id.
     * @param int $id
     * @return Ticket|null
     */
    public function find (int $id)
    {
        return $this->prepare()->find($id);
    }

    /**
     * @param int $id
     * @return Ticket|null
     */
    public function findOrFail (int $id)
    {
        return $this->prepare()->findOrFail($id);
    }

    /**
     * Get the first item of the result set.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first ()
    {
        return $this->prepare()->first();
    }

    /**
     * Get a collection of results.
     * @return \Illuminate\Support\Collection
     */
    public function get () : Collection
    {
        return $this->prepare()
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
        return $this->prepare()
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

    /**
     * Pre-run query prepare. Can be over-ridden.
     * @return Repository|\Illuminate\Database\Eloquent\Builder
     */
    protected function prepare ()
    {
        return $this->query();
    }
}
