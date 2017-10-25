<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Str;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Artisan;

class CreateSuperTest extends TestCase
{
    /**
     * @test
     */
    public function the_command_creates_a_supervisor ()
    {
        $user = $this->make->internalUser;

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

    /** @test */
    public function modifying_an_existing_agent_instead_of_creating_a_new_one ()
    {
        $user = $this->make->internalUser;

        $this->artisan('helpdesk:super', [
            'email' => $user->email,
        ]);

        $this->artisan('helpdesk:super', [
            'email' => $user->email,
        ]);

        $this->assertCount(1, Agent::query()->where('user_id', $user->id)->get());

        $user = $this->make->internalUser;
        $agent = $this->make->agent($user);

        $this->artisan('helpdesk:super', [
            'email' => $agent->user->email,
        ]);

        $this->assertCount(
            1,
            Agent::query()->where('user_id', $agent->user->id)->get()
        );
        $this->assertTrue($agent->fresh()->isSuper());
    }

    /** @test */
    public function it_uses_the_user_filter_callback ()
    {
        $external = $this->make->user;
        $internal = $this->make->internalUser;

        $this->artisan('helpdesk:super', [
            'email' => $external->email,
        ]);

        $this->artisan('helpdesk:super', [
            'email' => $internal->email,
        ]);

        $this->assertNull(Agent::query()->where(['user_id' => $external->id])->first());
        $this->assertNotNull(Agent::query()->where(['user_id' => $internal->id])->first());
        $this->assertTrue($internal->agent->isSuper());
    }
}
