<?php

namespace Aviator\Helpdesk\Tests\Traits;

use Aviator\Helpdesk\Tests\User;

trait VisitsAs
{
    /**
     * @param \Aviator\Helpdesk\Tests\User $who
     * @param string $where
     * @return \Aviator\Helpdesk\Tests\TestCase
     */
    protected function visitAs(User $who, $where)
    {
        return $this->actingAs($who)->visit($where);
    }
}
