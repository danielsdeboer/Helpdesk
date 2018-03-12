<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Route;

class UsersOnlyBKTest extends BKTestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::any('/guarded', ['middleware' => 'helpdesk.users', function () {
            return 'Guarded.';
        }]);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_aborts_with_403_for_guests ()
    {
        $this->get('/guarded')
            ->assertResponseStatus(302)
            ->assertRedirectedTo('login');
    }

    /**
     * @group middleware
     * @test
     */
    public function it_aborts_with_403_if_the_user_is_an_agent ()
    {
        $this->be($this->make->agent->user);

        $this->get('/guarded')
            ->assertResponseStatus(403);
    }

    /**
     * @group middleware
     * @test
     */
    public function it_continues_if_the_user_is_not_an_agent ()
    {
        $this->be($this->make->user);

        $this->visit('/guarded')
            ->see('Guarded.');
    }
}
