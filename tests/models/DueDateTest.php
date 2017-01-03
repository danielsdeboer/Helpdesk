<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\DueDate;
use Carbon\Carbon;

class DueDateTest extends TestCase {

    /**
     * @group duedate
     * @test
     */
    public function creating_a_due_date_creates_an_action_via_its_observer()
    {
        $dueDate = factory(DueDate::class)->create();

        $this->assertEquals('Due Dated', $dueDate->action->name);
    }

    /**
     * @group duedate
     * @test
     */
    public function the_due_on_field_is_cast_to_a_carbon_instance()
    {
        $dueDate = factory(DueDate::class)->create();

        $this->assertInstanceOf(Carbon::class, $dueDate->due_on);
    }
}