<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Opening;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\External\Opened;

class OpeningTest extends TestCase
{
    /**
     * @group model
     * @group model.opening
     * @test
     */
    public function creating_an_opening_creates_an_action_via_its_observer()
    {
        $opening = factory(Opening::class)->create();

        $this->assertEquals('Opened', $opening->action->name);
    }

    /**
     * @group model
     * @group model.opening
     * @test
     */
    public function creating_an_opening_fires_a_notification_to_the_end_user()
    {
        $opening = factory(Opening::class)->create();

        Notification::assertSentTo(
            $opening->ticket->user,
            Opened::class
        );
    }
}
