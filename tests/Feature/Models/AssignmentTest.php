<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\ModelTestCase;

class AssignmentTest extends ModelTestCase
{
    /** @test */
    public function creating_an_assignment_creates_an_action_via_the_assignment_observer()
    {
        $assignment = $this->make->assignment;

        $this->assertEquals('Assigned', $assignment->action->name);
    }

    /** @test */
    public function creating_an_assignment_fires_a_notification_to_the_assignee()
    {
        $assignment = $this->make->assignment;

        $this->assertSentTo($assignment->assignee->user);
    }

    /** @test */
    public function if_agent_doesnt_exist_dont_send_notification()
    {
        Assignment::query()->create([
            'ticket_id' => factory(Ticket::class)->create()->id,
            'assigned_to' => 9382,
            'agent_id' => null,
            'is_visible' => false,
        ]);

        $this->assertNotSentTo(Agent::all());
    }

    /** @test */
    public function if_ticket_is_ignored_user_doesnt_receive_notification()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;

        $this->addIgnoredUser([$ignoredUser->email]);

        $ticket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
        ]);

        Assignment::query()->create([
            'ticket_id' => $ticket->id,
            'assigned_to' => $agent->id,
            'agent_id' => null,
            'is_visible' => false,
        ]);

        $this->assertNotSentTo($agent->user);
    }
}
