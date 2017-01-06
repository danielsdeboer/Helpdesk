<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Tests\TestCase;

class ActionTest extends TestCase {

    /**
     * @group action
     * @test
     */
    public function it_has_an_object()
    {
        $action = factory(Action::class)->create();

        $this->assertNotNull($action->object);
    }

    /**
     * @group action
     * @test
     */
    public function it_has_an_subject()
    {
        $action = factory(Action::class)->create();

        $this->assertNotNull($action->subject);
    }
}
