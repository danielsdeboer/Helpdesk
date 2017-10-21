<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\External\Opened;

class OpeningTest extends TestCase
{
    /** @test */
    public function creating_an_opening_creates_an_action_via_its_observer()
    {
        $opening = $this->make->opening;

        $this->assertEquals('Opened', $opening->action->name);
    }

    /** @test */
    public function creating_an_opening_fires_a_notification_to_the_end_user()
    {
        $opening = $this->make->opening;

        /** @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $opening->ticket->user,
            Opened::class
        );
    }
}
