<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Middleware\AgentsOnly;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AgentsOnlyTest extends TestCase {


    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded', ['middleware' => AgentsOnly::class, function () {
            return 'Guarded.';
        }]);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_aborts_with_403_if_the_user_isnt_an_agent()
    {
        // A bare request with no user resolved to it
        $request = Request::create('http://example.com/agents/only', 'GET');

        try {
            $response = (new AgentsOnly)->handle($request, function() {});
        } catch (HttpException $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @group middleware
     * @test
     */
    public function it_passes_if_the_user_is_an_agent()
    {
        $user = factory(Agent::class)->create()->user;
        $this->be($user);

        $response = $this->call('GET', '/guarded');

        $this->assertEquals('Guarded.', $response->getContent());
    }
}
