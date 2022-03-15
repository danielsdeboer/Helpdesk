<?php

namespace Aviator\Helpdesk\Tests\Feature\Middleware;

use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class SupervisorsOnlyTest extends TestCase
{
    /** @var string */
    protected $url = '/guarded';

    /*
     * Create a route to test against.
     */
    public function setUp (): void
    {
        parent::setUp();

        Route::any($this->url, [
            'middleware' => 'helpdesk.supervisors',
            function () {
                return 'Guarded.';
            },
        ]);
    }

    /** @test */
    public function guests_get_a_403 ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function users_get_a_403 ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_super_agents_get_a_403 ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_continues_for_supervisors ()
    {
        $this->be($this->make->super->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->assertSee('Guarded.');
    }
}
