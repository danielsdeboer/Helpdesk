<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Notifications\Generic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

abstract class ModelTestCase extends TestCase
{
    /**
     * @param $notifiable
     */
    protected function assertSentTo ($notifiable)
    {
        Notification::assertSentTo(
            $notifiable,
            Generic::class,
            function ($notification) use ($notifiable) {
                $mailData = $notification->toMail($notifiable)->toArray();

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

    /**
     * @param $notifiable
     */
    protected function assertNotSentTo ($notifiable)
    {
        Notification::assertNotSentTo(
            $notifiable,
            Generic::class
        );
    }

    protected function withEvents()
    {
        Model::setEventDispatcher(
            app('events')
        );
    }

    /**
     * Mock the event dispatcher so all events are silenced and collected.
     */
    protected function withoutEvents ()
    {
        Model::unsetEventDispatcher();
    }
}
