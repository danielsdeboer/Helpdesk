<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Route;

class SupervisorsOnlyTest extends TestCase
{
    /** @const string */
    const URI = '/guarded';

    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded', ['middleware' => 'helpdesk.supervisors', function () {
            return 'Guarded.';
        }]);
    }

    /**
     * @test
     */
    public function guests_get_a_403 ()
    {
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /**
     * @test
     */
    public function users_get_a_403 ()
    {
        $this->be(
            factory(User::class)->create()
        );

        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /**
     * @test
     */
    public function agents_get_a_403 ()
    {
        $this->be(
            factory(Agent::class)->create()->user
        );

        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_passes_if_the_user_is_a_supervisor()
    {
        $this->be(
            $this->make->super->user
        );

        $this->visit(self::URI);

        $this->assertResponseOk();
        $this->assertEquals('Guarded.', $this->response->getContent());
    }
}
