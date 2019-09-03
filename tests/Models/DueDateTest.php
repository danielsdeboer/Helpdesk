<?php

namespace Aviator\Helpdesk\Tests\Models;

use Aviator\Helpdesk\Tests\TestCase;
use Carbon\Carbon;

class DueDateTest extends TestCase
{
    /** @test */
    public function creating_a_due_date_creates_an_action_via_its_observer()
    {
        $dueDate = $this->make->dueDate;

        $this->assertEquals('Due Date Added', $dueDate->action->name);
    }

    /** @test */
    public function the_due_on_field_is_cast_to_a_carbon_instance()
    {
        $dueDate = $this->make->dueDate;

        $this->assertInstanceOf(Carbon::class, $dueDate->due_on);
    }
}
