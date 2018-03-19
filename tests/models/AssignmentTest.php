<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Tests\Models\ModelTestCase;

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
}
