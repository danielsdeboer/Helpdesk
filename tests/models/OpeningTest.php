<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Notifications\External\Opened;
use Illuminate\Support\Facades\Notification;

class OpeningTest extends TestCase {

    /**
     * @group opening
     * @test
     */
    public function creating_an_opening_creates_an_action_via_its_observer()
    {
        $opening = factory(Opening::class)->create();

        $this->assertEquals('Opened', $opening->action->name);
    }

    /**
     * @group opening
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