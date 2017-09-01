<?php

namespace Aviator\Helpdesk\Tests\Traits;

use Aviator\Helpdesk\Tests\User;

trait CallsAs
{
    /**
     * @param \Aviator\Helpdesk\Tests\User $who
     * @param string $where
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function callAs(User $who, $where)
    {
        $this->actingAs($who);

        return $this->call('GET', $where);
    }
}
