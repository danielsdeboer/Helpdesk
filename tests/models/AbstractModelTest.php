<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Database\Eloquent\Model;
use Aviator\Helpdesk\Notifications\Generic;
use Illuminate\Support\Facades\Notification;

abstract class AbstractModelTest extends TestCase
{
    protected function assertSentTo($user)
    {
        Notification::assertSentTo(
            $user,
            Generic::class,
            function ($notification) {
                return $notification->address === config('helpdesk.from.address')
                    && $notification->name === config('helpdesk.from.name');
            }
        );
    }

    protected function assertNotSentTo($user)
    {
        Notification::assertNotSentTo(
            $user,
            Generic::class
        );
    }

    protected function withEvents()
    {
        Model::setEventDispatcher(app('events'));
    }

    protected function withoutEvents()
    {
        Model::unsetEventDispatcher();
    }
}
