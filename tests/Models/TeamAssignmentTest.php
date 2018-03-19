<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Tests\ModelTestCase;

class TeamAssignmentTest extends ModelTestCase
{
    /** @test */
    public function creating_a_team_assignment_creates_an_action_via_the_team_assignment_observer()
    {
        $assignment = $this->make->teamAssignment;

        $this->assertEquals('Assigned To Team', $assignment->action->name);
    }

    /** @test */
    public function create_an_assignment_fires_a_notification_to_the_assignee()
    {
        $team = $this->make->team;
        $assignment = $this->make->teamAssignment($team);
        $this->make->agent->makeTeamLeadOf($team);

        $this->assertSentTo($assignment->team->teamLeads);
    }
}
