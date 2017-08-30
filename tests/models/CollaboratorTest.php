<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Notification;

class CollaboratorTest extends TestCase
{
    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function creating_an_collaborator_creates_an_action_via_the_collaborator_observer()
    {
        $collaborator = $this->buildCollaborator();

        $this->assertEquals('Added', $collaborator->action->name);
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $collaborator = $this->buildCollaborator();

        Notification::assertSentTo(
            $collaborator->agent->user,
            \Aviator\Helpdesk\Notifications\Internal\Collaborator::class
        );
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function a_collaborator_has_an_agent ()
    {
        $collaborator = $this->buildCollaborator();

        $this->assertInstanceOf(Agent::class, $collaborator->agent);
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function a_collaborator_has_a_ticket ()
    {
        $collaborator = $this->buildCollaborator();

        $this->assertInstanceOf(Ticket::class, $collaborator->ticket);
    }

    /**
     * @return mixed
     */
    protected function buildCollaborator ()
    {
        $collaborator = factory(Collaborator::class)->create();

        return $collaborator;
    }
}
