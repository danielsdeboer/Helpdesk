<?php

namespace Aviator\Helpdesk\Queries;

interface QueryInterface
{
    public static function make(...$args);
    public function query();
}
