<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\External\Replied as ExternalReply;
use Aviator\Helpdesk\Notifications\Internal\Replied as InternalReply;

class ReplyTest extends TestCase
{
    /** @test */
    public function it_creates_an_action_via_its_observer()
    {
        $reply = $this->make->reply;

        $this->assertEquals('Reply Added', $reply->action->name);
    }

    /** @test */
    public function it_is_visible_by_default()
    {
        $reply = $this->make->reply;

        $this->assertTrue($reply->is_visible);
    }

    /** @test */
    public function it_sends_a_notification_to_the_user_if_created_by_an_agent()
    {
        $reply = $this->make->reply;

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $reply->ticket->user,
            ExternalReply::class
        );
    }

    /** @test */
    public function it_sends_a_notification_to_the_agent_if_created_by_an_user_and_assigned()
    {
        $ticket = $this->make->ticket
            ->assignToAgent($this->make->agent)
            ->externalReply('this is a reply', $this->make->user);

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $ticket->assignment->assignee,
            InternalReply::class
        );
    }
}
