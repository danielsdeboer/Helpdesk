<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Collaborator;
use Illuminate\Support\Facades\Notification;

class CollaboratorTest extends TestCase
{
    /*
     * Setup -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @return \Aviator\Helpdesk\Models\Collaborator
     */
    protected function createCollab()
    {
        return factory(Collaborator::class)->create();
    }

    /*
     * Tests -----------------------------------------------------------------------------------------------------------
     */

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function creating_an_collaborator_creates_an_action_via_the_collaborator_observer()
    {
        $collab = $this->createCollab();

        $this->assertEquals('Collaborator Added', $collab->action->name);
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $collab = $this->createCollab();

        Notification::assertSentTo(
            $collab->agent->user,
            \Aviator\Helpdesk\Notifications\Internal\Collaborator::class
        );
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function a_collaborator_has_an_agent()
    {
        $collab = $this->createCollab();

        $this->assertInstanceOf(Agent::class, $collab->agent);
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function a_collaborator_has_a_ticket()
    {
        $collab = $this->createCollab();

        $this->assertInstanceOf(Ticket::class, $collab->ticket);
    }

    /**
     * @group model
     * @group model.collab
     * @test
     */
    public function a_collaborator_has_a_creator()
    {
        $collab = $this->createCollab();

        $this->assertInstanceOf(Agent::class, $collab->createdBy);
    }
}
