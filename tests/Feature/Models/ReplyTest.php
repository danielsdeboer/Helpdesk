<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\ModelTestCase;
use Aviator\Helpdesk\Tests\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class ReplyTest extends ModelTestCase
{
    #[Test]
    public function it_creates_an_action_via_its_observer()
    {
        $reply = $this->make->reply;

        $this->assertEquals('Reply Added', $reply->action->name);
    }

    #[Test]
    public function it_is_visible_by_default()
    {
        $reply = $this->make->reply;

        $this->assertTrue($reply->is_visible);
    }

    #[Test]
    public function it_sends_a_notification_to_the_user_if_created_by_an_agent()
    {
        $reply = $this->make->reply;

        $this->assertSentTo($reply->ticket->user);
    }

    #[Test]
    public function it_sends_a_notification_to_the_agent_if_created_by_an_user_and_assigned()
    {
        $ticket = $this->make->ticket
            ->assignToAgent($this->make->agent)
            ->externalReply('this is a reply', $this->make->user);

        $this->assertSentTo($ticket->assignment->assignee);
    }

    #[Test]
    public function if_user_doesnt_exist_dont_send_notification_to_agent()
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::query()->create([
            'user_id' => User::factory()->create()->id,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);

        Reply::query()->create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => 9328,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo(Agent::all());
    }

    #[Test]
    public function if_the_ticket_is_not_assigned_dont_notify_an_agent()
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::query()->create([
            'user_id' => User::factory()->create()->id,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $agent->delete();

        Reply::query()->create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $this->make->user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo(Agent::all());
    }

    #[Test]
    public function if_agent_doesnt_exist_dont_send_notification_to_user()
    {
        $user = User::factory()->create();

        $this->withoutEvents();

        /** @var Ticket $ticket */
        $ticket = Ticket::query()->create([
            'user_id' => $user->id,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,

        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $agent->delete();

        $this->withEvents();

        Reply::query()->create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo($user);
    }

    #[Test]
    public function if_user_doesnt_exist_dont_send_notification()
    {
        $user = User::factory()->create();

        $this->withoutEvents();

        /** @var Ticket $ticket */
        $ticket = Ticket::query()->create([
            'user_id' => $user->id,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
        ]);

        $agent = $this->make->agent;
        $ticket->assignToAgent($agent);
        $user->delete();

        $this->withEvents();

        Reply::query()->create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'is_visible' => true,
        ]);

        $this->assertNotSentTo($user);
    }

    #[Test]
    public function it_doesnt_send_a_notification_to_assignee_if_ignored()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;

        $this->addIgnoredUser([$ignoredUser->email]);

        $ticket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => GenericContent::factory()->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
            'is_ignored' => Carbon::now()->toDateTimeString(),
        ]);

        $ticket->assignToAgent($agent);

        Reply::query()->create([
            'ticket_id' => $ticket->id,
            'body' => 'Something',
            'agent_id' => $agent->id,
            'user_id' => $ignoredUser->id,
            'is_visible' => true,
        ]);

        $this->assertSentTo($ignoredUser);
        $this->assertNotSentTo($agent);
    }
}
