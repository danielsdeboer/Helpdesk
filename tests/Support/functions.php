<?php

use Aviator\Helpdesk\Tests\Support\Make;

/**
 * @param string $method
 * @param array ...$args
 * @return mixed
 */
function tm (string $method, ...$args)
{
    return (new Make)->$method(...$args);
}
