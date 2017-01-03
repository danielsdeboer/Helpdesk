<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Email;
use Aviator\Helpdesk\Notifications\External\Emailed;
use Illuminate\Support\Facades\Notification;

class EmailTest extends TestCase {

    /**
     * @group email
     * @test
     */
    public function creating_an_email_creates_an_action_via_its_observer()
    {
        $email = factory(Email::class)->create();

        $this->assertEquals('Emailed', $email->action->name);
    }

    /**
     * @group email
     * @test
     */
    public function creating_an_email_sends_a_notification_to_the_end_user()
    {
        $email = factory(Email::class)->create();

        Notification::assertSentTo(
            $email->ticket->user,
            Emailed::class
        );
    }
}