<?php

namespace Aviator\Helpdesk\Tests;

class AgentTest extends BKTestCase
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
}
