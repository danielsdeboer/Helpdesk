<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\TestCase;

class AgentTest extends TestCase
{
    /** @test */
    public function it_belongs_to_a_user()
    {
        $agent = $this->make->agent;

        $this->assertNotNull($agent->user);
    }

    /** @test */
    public function it_may_belong_to_many_teams()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $team1 = $this->make->team;
        $team2 = $this->make->team;

        $agent1->addToTeam($team1);
        $agent2->addToTeams([$team1, $team2]);

        $this->assertEquals(1, $agent1->teams->count());
        $this->assertEquals(2, $agent2->teams->count());
    }

    /** @test */
    public function it_may_be_added_to_a_team()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->addToTeam($team);

        $this->assertEquals(1, $agent->teams->count());
    }

    /** @test */
    public function it_may_be_removed_from_a_team()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->addToTeam($team);

        $this->assertEquals(1, $agent->teams->count());

        $agent = $agent->fresh();
        $agent->removeFromTeam($team);

        $this->assertEquals(0, $agent->teams->count());
    }

    /** @test */
    public function it_may_be_added_to_many_teams()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $team2 = $this->make->team;

        $agent->addToTeams([$team, $team2]);

        $this->assertEquals(2, $agent->teams->count());
    }

    /** @test */
    public function it_may_be_removed_from_many_teams()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $team2 = $this->make->team;
        $team3 = $this->make->team;

        $agent->addToTeams([$team, $team2, $team3]);

        $this->assertEquals(3, $agent->teams->count());

        $agent = $agent->fresh();
        $agent->removeFromTeams([$team, $team2]);

        $this->assertEquals(1, $agent->teams->count());
    }

    /** @test */
    public function it_may_be_made_team_lead_of_a_team()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);
    }

    /** @test */
    public function it_may_be_made_team_lead_of_a_team_it_already_belongs_to()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->teams()->attach($team->id);

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);
    }

    /** @test */
    public function it_may_be_made_team_lead_and_then_removed_as_team_lead()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);

        $agent = $agent->fresh();

        $agent->removeTeamLeadOf($team);

        $this->assertEquals(0, $agent->teams->first()->pivot->is_team_lead);
    }

    /** @test */
    public function isMemberOfReturnsTrueIfAnAgentIsAMemberOfThatTeam()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->makeTeamLeadOf($team);

        $this->assertTrue($agent->isMemberOf($team));
    }

    /** @test */
    public function isMemberOfReturnsFalseIfAnAgentIsntAMemberOfThatTeam()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $this->assertFalse($agent->isMemberOf($team));
    }

    /**
     * @test
     */
    public function checking_if_an_agent_is_super ()
    {
        $agent = $this->make->agent;
        $super = $this->make->super;

        $this->assertSame(false, $agent->isSuper());
        $this->assertSame(true, $super->isSuper());

        $this->assertSame(false, $agent->is_super);
        $this->assertSame(true, $super->is_super);
    }

    /** @test */
    public function it_can_check_if_an_agent_is_lead_of_a_team ()
    {
        $agent = $this->make->agent;
        $leadOf = $this->make->team;
        $notLeadOf = $this->make->team;
        $agent->makeTeamLeadOf($leadOf);

        $this->assertTrue($agent->isLeadOf($leadOf));
        $this->assertFalse($agent->isLeadOf($notLeadOf));
    }

    /** @test */
    public function it_can_check_if_an_agent_is_lead_for_a_ticket ()
    {
        $team1 = $this->make->team;
        $team2 = $this->make->team;
        $agent = $this->make->agent->makeTeamLeadOf($team1);
        $ticket1 = $this->make->ticket->assignToTeam($team1);
        $ticket2 = $this->make->ticket->assignToTeam($team2);

        $this->assertTrue($agent->isLeadFor($ticket1));
        $this->assertFalse($agent->isLeadFor($ticket2));
    }

    /** @test */
    public function it_can_scope_to_a_team ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;
        $agent3 = $this->make->agent;
        $agent4 = $this->make->agent;

        $team = $this->make->team->addMember($agent1);

        $all = Agent::all();
        $scoped = Agent::inTeam($team)->get();

        $this->assertCount(6, $all);
        $this->assertCount(1, $scoped);
        $this->assertSame($agent1->id, $scoped->first()->id);

        $team->addMember($agent4);

        $scoped = Agent::inTeam($team)->get();

        $this->assertCount(2, $scoped);
    }
}
