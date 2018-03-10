<?php

namespace Aviator\Helpdesk\Tests;

use Carbon\Carbon;

class DatabaseTest extends TestCase
{
    /**
     * @group database
     * @test
     */
    public function the_database_contains_users()
    {
        $users = factory(config('helpdesk.userModel'), 10)->create();

        $this->assertEquals($users->count(), 10);
    }

    /**
     * @group database
     * @test
     */
    public function a_persisted_user_has_an_email()
    {
        $user = User::create([
            'email' => 'test@user.com',
        ]);

        $this->assertEquals($user->email, 'test@user.com');
    }
}
