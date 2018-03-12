<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\GenericContent;

class ReplyTest extends AbstractModelBKTest
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

        $this->assertSentTo($reply->ticket->user);
    }

    /** @test */
    public function it_sends_a_notification_to_the_agent_if_created_by_an_user_and_assigned()
    {
        $ticket = $this->make->ticket
            ->assignToAgent($this->make->agent)
            ->externalReply('this is a reply', $this->make->user);

        $this->assertSentTo($ticket->assignment->assignee);
    }

    /** @test */
    public function if_user_doesnt_exist_dont_send_notification_to_agent()
    {
        $ticket = Ticket::create([
            'user_id' => factory(config('helpdesk.userModel'))->create()->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);

        Reply::create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => 9328,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo(Agent::all());
    }

    /** @test */
    public function if_the_ticket_is_not_assigned_dont_notify_an_agent()
    {
        $ticket = Ticket::create([
            'user_id' => factory(config('helpdesk.userModel'))->create()->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $agent->delete();

        Reply::create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $this->make->user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo(Agent::all());
    }

    /** @test */
    public function if_agent_doesnt_exist_dont_send_notification_to_user()
    {
        $user = factory(User::class)->create();

        $this->withoutEvents();

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $agent->delete();

        $this->withEvents();

        Reply::create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo($user);
    }

    /** @test */
    public function if_user_doesnt_exist_dont_send_notification()
    {
        $user = factory(User::class)->create();

        $this->withoutEvents();

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $user->delete();

        $this->withEvents();

        Reply::create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo($user);
    }
}
