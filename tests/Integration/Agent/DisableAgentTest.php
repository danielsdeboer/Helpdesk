<?php

namespace Aviator\Helpdesk\Tests\Integration\Agent;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\TestCase;

class DisableAgentTest extends TestCase
{
    /** @test */
    public function disable_agent_puts_agent_onto_disabled_list_removes_from_teams()
    {
        $team = $this->make->team;
        $super = $this->make->super;
        $agent = $this->make->agent;

        $super->addToTeam($team);
        $agent->addToTeam($team);

        $this->be($super->user);

        $response = $this->patch('helpdesk/admin/agents/4',
            [
                'user_id' => $agent->user_id,
            ]
        );

        $response->assertRedirect('helpdesk/admin/disabled');
        $this->assertSame(1, $team->agents->count());
        $this->assertSame(3, Agent::enabled()->get()->count());
    }
}
