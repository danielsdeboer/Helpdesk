<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class CreateSuperBKTest extends BKTestCase
{
    /**
     * @test
     */
    public function it_creates_a_supervisor_agent ()
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
    public function it_stops_and_outputs_an_error_if_no_user_can_be_found ()
    {
        $this->artisan('helpdesk:super', [
            'email' => 'some@user.net',
        ]);

        $this->assertTrue(
            Str::contains(Artisan::output(), 'No user found for some@user.net')
        );
    }

    /** @test */
    public function it_modifies_existing_users ()
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
