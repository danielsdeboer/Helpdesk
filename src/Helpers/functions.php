<?php

use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;

/**
 * @return \Aviator\Helpdesk\Interfaces\NotificationFactoryInterface
 */
function notification()
{
    return app(NotificationFactoryInterface::class);
}

/**
 * @param object $initialObject
 * @return mixed
 */
function reduceProperties($initialObject, string $path)
{
    return array_reduce(
        explode('.', $path),
        function ($object, $prop) {
            if ($object && is_object($object)) {
                return $object->$prop;
            }
        },
        $initialObject
    );
}

/**
 * @return mixed
 */
function hd_route(string $name)
{
    return config('helpdesk.routes.' . $name);
}

/**
 * Get the current agent. Return null if guest or user.
 *
 * @return \Aviator\Helpdesk\Models\Agent|null
 */
function hd_agent()
{
    return auth()->user()->agent ?? null;
}

/**
 * @return bool
 */
function hd_is_agent()
{
    return auth()->user() && auth()->user()->agent;
}
