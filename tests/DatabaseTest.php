<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Tests\User;
use PHPUnit\Framework\Attributes\Test;

class DatabaseTest extends BKTestCase
{
    /**
     * @group database
     */
    #[Test]
    public function the_database_contains_users()
    {
        $users = User::factory()->count(10)->create();

        $this->assertEquals($users->count(), 10);
    }

    /**
     * @group database
     */
    #[Test]
    public function a_persisted_user_has_an_email()
    {
        $user = User::create([
            'email' => 'test@user.com',
        ]);

        $this->assertEquals($user->email, 'test@user.com');
    }
}
