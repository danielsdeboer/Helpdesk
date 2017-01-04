<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\InternalReply;
use Aviator\Helpdesk\Notifications\External\Replied;
use Illuminate\Support\Facades\Notification;

class InternalReplyTest extends TestCase {

    /**
     * @group internalReply
     * @test
     */
    public function creating_an_internal_reply_creates_an_action_via_its_observer()
    {
        $internalReply = factory(InternalReply::class)->create();

        $this->assertEquals('Internal Reply Added', $internalReply->action->name);
    }

    /**
     * @group internalReply
     * @test
     */
    public function an_internal_reply_is_visible_by_default()
    {
        $internalReply = factory(InternalReply::class)->create();

        $this->assertTrue($internalReply->is_visible);
    }

    /**
     * @group internalReply
     * @test
     */
    public function creating_an_internal_reply_sends_a_notification_to_the_end_user()
    {
        $internalReply = factory(InternalReply::class)->create();

        Notification::assertSentTo(
            $internalReply->ticket->user,
            Replied::class
        );
    }
}