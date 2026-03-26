<?php

namespace Aviator\Helpdesk\Tests\Feature\Middleware;

use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;

class AgentsOnlyTest extends TestCase
{
    /** @var string */
    protected $url = '/guarded';

    /*
     * Set up a testing route.
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::any($this->url, ['middleware' => 'helpdesk.agents', function () {
            return 'Guarded.';
        }]);
    }

    #[Test]
    public function it_redirects_guests_to_login()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    #[Test]
    public function it_throws_a_403_for_non_agent()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_continues_for_agents()
    {
        $this->be($this->make->agent->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
        $response->assertSee('Guarded.');
    }
}
