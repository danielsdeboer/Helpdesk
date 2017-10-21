<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Notifications\Internal\Collaborator;
use Illuminate\Support\Facades\Notification;

class CollaboratorTest extends TestCase
{
    /** @test */
    public function creating_an_collaborator_creates_an_action_via_the_collaborator_observer()
    {
        $collab = $this->make->collaborator;

        $this->assertEquals('Collaborator Added', $collab->action->name);
    }

    /** @test */
    public function creating_an_assignment_fires_a_notification_to_the_assignee()
    {
        $collab = $this->make->collaborator;

        /** @noinspection PhpUndefinedMethodInspection */
        Notification::assertSentTo(
            $collab->agent->user,
            Collaborator::class
        );
    }

    /** @test */
    public function a_collaborator_has_an_agent()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Agent::class, $collab->agent);
    }

    /** @test */
    public function a_collaborator_has_a_ticket()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Ticket::class, $collab->ticket);
    }

    /** @test */
    public function a_collaborator_has_a_creator()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Agent::class, $collab->createdBy);
    }
}
