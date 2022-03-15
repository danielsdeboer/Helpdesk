<?php

namespace Aviator\Helpdesk\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Models\Team team
 */
class Get
{
    protected string $namespace = '\\Aviator\\Helpdesk\\Models\\';
    protected bool $orderByLatest = false;
    protected bool $getCount = false;
    protected bool $includeSoftDeleted = false;

    /**
     * @param $name
     * @return mixed
     */
    public function getModel ($name)
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->namespace . ucfirst($name);

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $model::query()
            ->when($this->orderByLatest, function (Builder $query) {
                $query->latest();
            })
            ->when($this->includeSoftDeleted, function (Builder $query) {
                /* @noinspection PhpUndefinedMethodInspection */
                $query->withTrashed();
            });

        return $this->getCount
            ? $query->count()
            : $query->first();
    }

    /**
     * @param $name
     * @return $this
     */
    public function __get ($name)
    {
        if ($name === 'latest') {
            $this->orderByLatest = true;

            return $this;
        }

        if ($name === 'count') {
            $this->getCount = true;

            return $this;
        }

        if ($name === 'withTrashed') {
            $this->includeSoftDeleted = true;

            return $this;
        }

        return $this->getModel($name);
    }
}
