<?php

/**
 * @return \Aviator\Helpdesk\Interfaces\NotificationFactoryInterface
 */
function notification ()
{
    return app(\Aviator\Helpdesk\Interfaces\NotificationFactoryInterface::class);
}