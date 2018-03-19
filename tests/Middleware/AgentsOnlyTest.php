<?php

namespace Aviator\Helpdesk\Tests\Middleware;

use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class AgentsOnlyTest extends TestCase
{
    /** @var string */
    protected $url = '/guarded';

    /*
     * Set up a testing route.
     */
    public function setUp()
    {
        parent::setUp();

        Route::any($this->url, ['middleware' => 'helpdesk.agents', function () {
            return 'Guarded.';
        }]);
    }

    /** @test */
    public function it_redirects_guests_to_login ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /** @test */
    public function it_throws_a_403_for_non_agent ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_continues_for_agents ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->assertSee('Guarded.');
    }
}
