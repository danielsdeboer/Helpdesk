<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ActionTest extends TestCase
{
    #[Test]
    public function it_has_an_object()
    {
        $action = $this->make->action;

        $this->assertNotNull($action->object);
    }

    #[Test]
    public function it_has_an_subject()
    {
        $action = $this->make->action;

        $this->assertNotNull($action->subject);
    }
}
