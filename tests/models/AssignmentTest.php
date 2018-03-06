<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Assignment;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\Internal\AssignedToAgent;

class AssignmentTest extends TestCase
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

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $assignment->assignee->user,
            AssignedToAgent::class
        );
    }

    /** @test */
    public function if_agent_doesnt_exist_dont_send_notification()
    {
        Assignment::create([
            'ticket_id' => factory(Ticket::class)->create()->id,
            'assigned_to' => 9382,
            'agent_id' => null,
            'is_visible' => false,
        ]);

        /* @noinspection PhpUndefinedMethodInspection */
        Notification::assertNotSentTo(
            Agent::all(),
            AssignedToAgent::class
        );
    }
}
