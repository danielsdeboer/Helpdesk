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
function helpdeskRoute (string $name)
{
    return config('helpdesk.routes.' . $name);
}

/**
 * @return bool
 */
function isAgent ()
{
    return auth()->user() && auth()->user()->agent;
}
