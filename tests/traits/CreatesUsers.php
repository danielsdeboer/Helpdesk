<?php

namespace Aviator\Helpdesk\Tests\Traits;

use Aviator\Helpdesk\Tests\User;

trait CreatesUsers
{
    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    protected function createUser()
    {
        return factory(User::class)->create();
    }
}
