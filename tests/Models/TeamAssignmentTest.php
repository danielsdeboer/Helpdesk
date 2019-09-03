<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Models\Ticket;
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
    public function creating_a_team_assignment_fires_a_notification_to_the_team_leads ()
    {
        $user = $this->make->user;
        $team = $this->make->team;

        $this->make->agent->makeTeamLeadOf($team);
        $assignment = $this->make->teamAssignment($team);

        foreach ($assignment->team->teamLeads as $teamLead) {
            $this->assertSentTo($teamLead->user);
        }
    }

    /** @test */
    public function it_doesnt_send_a_notification_to_team_lead_if_from_ignored_user ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;
        $team = $this->make->team;

        $this->addIgnoredUser([$ignoredUser->email]);

        $agent->makeTeamLeadOf($team);

        $ticket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 1,
        ]);

        $assignment = TeamAssignment::query()->create([
            'ticket_id' => $ticket->id,
            'agent_id' => null,
            'team_id' => $team->id,
            'is_visible' => false,
        ]);

        foreach ($assignment->team->teamLeads as $teamLead) {
            $this->assertNotSentTo($teamLead->user);
        }
    }
}
