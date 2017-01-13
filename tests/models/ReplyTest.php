<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Notification;

class ReplyTest extends TestCase {

    /**
     * @group model
     * @group model.reply
     * @test
     */
    public function it_creates_an_action_via_its_observer()
    {
        $reply = factory(Reply::class)->create();

        $this->assertEquals('Reply Added', $reply->action->name);
    }

    /**
     * @group model
     * @group model.reply
     * @test
     */
    public function it_is_visible_by_default()
    {
        $reply = factory(Reply::class)->create();

        $this->assertTrue($reply->is_visible);
    }

    /**
     * @group model
     * @group model.reply
     * @test
     */
    public function it_sends_a_notification_to_the_user_if_created_by_an_agent()
    {
        $reply = factory(Reply::class)->create();

        Notification::assertSentTo(
            $reply->ticket->user,
            \Aviator\Helpdesk\Notifications\External\Replied::class
        );
    }

    /**
     * @group model
     * @group model.reply
     * @test
     */
    public function it_sends_a_notification_to_the_agent_if_created_by_an_user_and_assigned()
    {
        $ticket = factory(Ticket::class)
            ->create()
            ->assignToAgent(factory(Agent::class)->create())
            ->externalReply('this is a reply', factory(User::class)->create());

        Notification::assertSentTo(
            $ticket->assignment->assignee,
            \Aviator\Helpdesk\Notifications\Internal\Replied::class
        );
    }
}