<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\Notifications\Internal\Collaborator;
use Aviator\Helpdesk\Models\Collaborator as CollaboratorModel;

class CollaboratorTest extends AbstractModelTest
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

        $this->assertSentTo($collab->agent->user);
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

    /** @test */
    public function if_collaborator_doesnt_exist_dont_send_notification()
    {
        CollaboratorModel::create([
            'agent_id' => 9932,
            'ticket_id' => factory(Ticket::class)->create()->id,
            'created_by' => factory(Agent::class)->create(),
        ]);

        $this->assertNotSentTo(CollaboratorModel::all());
    }
}
