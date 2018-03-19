<?php

/**
 * @return \Aviator\Helpdesk\Interfaces\NotificationFactoryInterface
 */
function notification ()
{
    return app(\Aviator\Helpdesk\Interfaces\NotificationFactoryInterface::class);
}

/**
 * @param object $initialObject
 * @param string $path
 * @return mixed
 */
function reduceProperties ($initialObject, string $path)
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
 * @param string $name
 * @return mixed
 */
function hd_route (string $name)
{
    return config('helpdesk.routes.' . $name);
}

/**
 * Get the current agent. Return null if guest or user.
 * @return \Aviator\Helpdesk\Models\Agent|null
 */
function hd_agent ()
{
    return auth()->user()->agent ?? null;
}

/**
 * @return bool
 */
function hd_is_agent ()
{
    return auth()->user() && auth()->user()->agent;
}
