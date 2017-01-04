<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Illuminate\Support\Facades\Notification;

class PoolTest extends TestCase {

    /**
     * @group pool
     * @test
     */
    public function a_pool_has_a_team_lead()
    {
        $pool = factory(Pool::class)->create();

        $this->assertNotNull($pool->teamLead->email);
    }
}