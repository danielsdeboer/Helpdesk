<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Route;

class SupervisorsOnlyTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded', ['middleware' => 'helpdesk.supervisors', function () {
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
    public function it_aborts_with_403_if_the_user_isnt_a_supervisor()
    {
        $this->be(factory(User::class)->create());
        $this->call('GET', '/guarded');

        $this->assertResponseStatus(403);

        $this->be(factory(Agent::class)->create()->user);
        $this->call('GET', '/guarded');

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_passes_if_the_user_is_a_supervisor()
    {
        $this->be(
            factory(Agent::class)->states('isSuper')->create()->user
        );
        $response = $this->call('GET', '/guarded');

        $this->assertEquals('Guarded.', $response->getContent());
    }
}
