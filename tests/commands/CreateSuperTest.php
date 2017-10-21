<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Str;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Artisan;

class CreateSuperTest extends AdminBase
{
    /**
     * @test
     */
    public function the_command_creates_a_supervisor ()
    {
        $user = factory(User::class)->create();

        $this->artisan('helpdesk:super', [
            'email' => $user->email,
        ]);

        /** @var \Aviator\Helpdesk\Models\Agent $super */
        $super = Agent::query()->where('user_id', $user->id)->first();

        $this->assertInstanceOf(Agent::class, $super);
        $this->assertTrue($super->isSuper());
        $this->assertTrue(
            Str::contains(Artisan::output(), 'Supervisor Agent created for ' . $user->email)
        );
    }

    /**
     * @test
     */
    public function the_command_errors_if_no_such_user ()
    {
        $this->artisan('helpdesk:super', [
            'email' => 'some@user.net',
        ]);

        $this->assertTrue(
            Str::contains(Artisan::output(), 'No user found for some@user.net')
        );
    }
}