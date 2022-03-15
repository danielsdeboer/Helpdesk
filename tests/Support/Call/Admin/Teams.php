<?php

namespace Aviator\Helpdesk\Tests\Support\Call\Admin;

use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Tests\Support\CallAbstract;
use Illuminate\Testing\TestResponse;

class Teams extends CallAbstract
{
    /**
     * @param Team|int $team
     */
    public function show ($team): TestResponse
    {
        if ($team instanceof Team) {
            $team = $team->getKey();
        }

        return $this->get(sprintf('helpdesk/admin/teams/%d', $team));
    }
}
