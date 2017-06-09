<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Route;

class AgentsOnlyTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded', ['middleware' => 'helpdesk.agents', function () {
            return 'Guarded.';
        }]);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_aborts_with_403_for_guests()
    {
        $this->call('GET', '/guarded');

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_aborts_with_403_if_the_user_isnt_an_agent()
    {
        $this->be(factory(User::class)->create());
        $this->call('GET', '/guarded');

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_passes_if_the_user_is_an_agent()
    {
        $this->be(factory(Agent::class)->create()->user);
        $response = $this->call('GET', '/guarded');

        $this->assertEquals('Guarded.', $response->getContent());
    }
}
