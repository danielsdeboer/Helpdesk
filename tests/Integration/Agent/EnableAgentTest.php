<?php

namespace Aviator\Helpdesk\Tests\Integration\Agent;

use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Models\Agent;
use Carbon\Carbon;

class EnableAgentTest extends TestCase
{
    /** @test */
    public function enable_agent_puts_agent_onto_agents_list()
    {
        $this->assertSame(2, Agent::enabled()->get()->count());

        $super = Agent::where('is_super', 1)->first();
        $agent = $this->make->agent;

        $agent->is_disabled = Carbon::now()->toDateTimeString();
        $agent->save();

        $this->assertEquals($agent->user_id, Agent::disabled()->first()->user_id);

        $this->be($super->user);

        $response = $this->patch('helpdesk/admin/disabled/3',
            [
                'user_id' => $agent->user_id,
            ]
        );

        $response->assertRedirect('helpdesk/admin/agents/3');
        $this->assertSame(3, Agent::enabled()->get()->count());
    }
}
