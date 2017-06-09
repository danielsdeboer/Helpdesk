<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;

class AgentTest extends TestCase
{
    /**
     * Build up an agent.
     * @return Agent
     */
    protected function agent($numberOfAgents = 1)
    {
        if ($numberOfAgents == 1) {
            return factory(Agent::class)->create();
        }

        return factory(Agent::class, $numberOfAgents)->create();
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $agent = factory(Agent::class)->create();

        $this->assertNotNull($agent->user);
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_belong_to_many_teams()
    {
        $agent = $this->agent();
        $agent2 = $this->agent();

        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();

        $agent2->addToTeams([$team, $team2]);

        $agent->addToTeam($team);

        $this->assertEquals(1, $agent->teams->count());
        $this->assertEquals(2, $agent2->teams->count());
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_added_to_a_team()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();

        $agent->addToTeam($team);

        $this->assertEquals(1, $agent->teams->count());
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_removed_from_a_team()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();

        $agent->addToTeam($team);

        $this->assertEquals(1, $agent->teams->count());

        $agent = $agent->fresh();
        $agent->removeFromTeam($team);

        $this->assertEquals(0, $agent->teams->count());
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_added_to_many_teams()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();

        $agent->addToTeams([$team, $team2]);

        $this->assertEquals(2, $agent->teams->count());
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_removed_from_many_teams()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();
        $team3 = factory(Pool::class)->create();

        $agent->addToTeams([$team, $team2, $team3]);

        $this->assertEquals(3, $agent->teams->count());

        $agent = $agent->fresh();
        $agent->removeFromTeams([$team, $team2]);

        $this->assertEquals(1, $agent->teams->count());
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_made_team_lead_of_a_team()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_made_team_lead_of_a_team_it_already_belongs_to()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();

        $agent->teams()->attach($team->id);

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);
    }

    /**
     * @group model
     * @group model.agent
     * @test
     */
    public function it_may_be_made_team_lead_and_then_removed_as_team_lead()
    {
        $agent = $this->agent();
        $team = factory(Pool::class)->create();

        $agent->makeTeamLeadOf($team);

        $this->assertEquals(1, $agent->teams->first()->pivot->is_team_lead);

        $agent = $agent->fresh();

        $agent->removeTeamLeadOf($team);

        $this->assertEquals(0, $agent->teams->first()->pivot->is_team_lead);
    }
}
