<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Email;

class EmailTest extends TestCase {

    /**
     * @group duedate
     * @test
     */
    public function creating_an_email_creates_an_action_via_its_observer()
    {
        $email = factory(Email::class)->create();

        $this->assertEquals('Emailed', $email->action->name);
    }
}