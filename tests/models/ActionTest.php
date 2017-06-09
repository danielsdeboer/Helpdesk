<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Action;

class ActionTest extends TestCase
{
    /**
     * @group model
     * @group model.action
     * @test
     */
    public function it_has_an_object()
    {
        $action = factory(Action::class)->create();

        $this->assertNotNull($action->object);
    }

    /**
     * @group model
     * @group model.action
     * @test
     */
    public function it_has_an_subject()
    {
        $action = factory(Action::class)->create();

        $this->assertNotNull($action->subject);
    }
}
