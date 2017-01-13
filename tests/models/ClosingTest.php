<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Notifications\External\Closed;
use Illuminate\Support\Facades\Notification;

class ClosingTest extends TestCase {

    /**
     * @group model
     * @group model.closing
     * @test
     */
    public function creating_a_closing_creates_an_action_via_its_observer()
    {
        $closing = factory(Closing::class)->create();

        $this->assertEquals('Closed', $closing->action->name);
    }

    /**
     * @group model
     * @group model.closing
     * @test
     */
    public function creating_an_closing_fires_a_notification_to_the_end_user()
    {
        $closing = factory(Closing::class)->create();

        Notification::assertSentTo(
            $closing->ticket->user,
            Closed::class
        );
    }
}