<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Database\Eloquent\Model;
use Aviator\Helpdesk\Notifications\Generic;
use Illuminate\Support\Facades\Notification;

abstract class AbstractModelBKTest extends BKTestCase
{
    protected function assertSentTo($user)
    {
        Notification::assertSentTo(
            $user,
            Generic::class,
            function ($notification) use ($user) {
                $mailData = $notification->toMail($user)->toArray();

                $this->assertSame($notification->address, config('helpdesk.from.address'));
                $this->assertSame($notification->name, config('helpdesk.from.name'));
                $this->assertSame($notification->subject, $mailData['subject']);
                $this->assertSame($notification->greeting, $mailData['greeting']);
                $this->assertSame($notification->line, $mailData['introLines'][0]);

                $this->assertFalse(filter_var($notification->route, FILTER_VALIDATE_URL));

                return true;
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
