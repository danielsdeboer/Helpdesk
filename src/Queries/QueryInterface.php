<?php

namespace Aviator\Helpdesk\Queries;

use Illuminate\Database\Eloquent\Builder;

interface QueryInterface
{
    /**
     * @param array ...$args
     * @return QueryInterface
     */
    public static function make (...$args);

    /**
     * @param array ...$args
     * @return Builder
     */
    public static function builder (...$args) : Builder;

    public function query();
}
