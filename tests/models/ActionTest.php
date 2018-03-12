<?php

namespace Aviator\Helpdesk\Tests;

class ActionTest extends BKTestCase
{
    /** @test */
    public function it_has_an_object()
    {
        $action = $this->make->action;

        $this->assertNotNull($action->object);
    }

    /** @test */
    public function it_has_an_subject()
    {
        $action = $this->make->action;

        $this->assertNotNull($action->subject);
    }
}
