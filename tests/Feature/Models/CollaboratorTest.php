<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\ModelTestCase;
use PHPUnit\Framework\Attributes\Test;

class CollaboratorTest extends ModelTestCase
{
    #[Test]
    public function creating_an_collaborator_creates_an_action_via_the_collaborator_observer()
    {
        $collab = $this->make->collaborator;

        $this->assertEquals('Collaborator Added', $collab->action->name);
    }

    #[Test]
    public function creating_an_assignment_fires_a_notification_to_the_assignee()
    {
        $collab = $this->make->collaborator;

        $this->assertSentTo($collab->agent->user);
    }

    #[Test]
    public function a_collaborator_has_an_agent()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Agent::class, $collab->agent);
    }

    #[Test]
    public function a_collaborator_has_a_ticket()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Ticket::class, $collab->ticket);
    }

    #[Test]
    public function a_collaborator_has_a_creator()
    {
        $collab = $this->make->collaborator;

        $this->assertInstanceOf(Agent::class, $collab->createdBy);
    }

    #[Test]
    public function if_collaborator_doesnt_exist_dont_send_notification()
    {
        Collaborator::query()->create([
            'agent_id' => 9932,
            'ticket_id' => Ticket::factory()->create()->id,
            'created_by' => Agent::factory()->create(),
        ]);

        $this->assertNotSentTo(Collaborator::all());
    }
}
