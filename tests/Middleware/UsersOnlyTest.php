<?php

namespace Aviator\Helpdesk\Tests\Middleware;

use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class UsersOnlyTest extends TestCase
{
    /** @var string */
    protected $url = '/guarded';

    /*
     * Set up a testing route.
     */
    public function setUp (): void
    {
        parent::setUp();

        Route::any($this->url, [
            'middleware' => 'helpdesk.users',
            function () {
                return 'Guarded.';
            },
        ]);
    }

    /** @test */
    public function it_aborts_with_403_for_guests ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    /** @test */
    public function it_aborts_with_403_if_the_user_is_an_agent ()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_continues_if_the_user_is_not_an_agent ()
    {
        $this->be($this->make->user);

        $response = $this->get('/guarded');

        $response->assertStatus(200);
        $response->assertSee('Guarded');
    }
}
