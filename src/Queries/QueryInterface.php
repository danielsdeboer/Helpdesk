<?php

namespace Aviator\Helpdesk\Queries;

interface QueryInterface
{
    public static function make(...$args);
    public static function builder(...$args);
    public function query();
}
