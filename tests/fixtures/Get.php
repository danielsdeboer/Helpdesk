<?php

namespace Aviator\Helpdesk\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Find.
 * @property \Aviator\Helpdesk\Tests\Get latest
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Tests\Get count
 * @property \Aviator\Helpdesk\Tests\Get withTrashed
 * @property \Aviator\Helpdesk\Models\Team team
 */
class Get
{
    protected $namespace = '\\Aviator\\Helpdesk\\Models\\';

    /** @var bool */
    protected $orderByLatest = false;

    /** @var bool */
    protected $getCount = false;

    /** @var bool */
    protected $includeSoftDeleted = false;

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
