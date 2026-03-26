<?php

namespace Aviator\Helpdesk\Tests\Feature\Models;

use Aviator\Helpdesk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NoteTest extends TestCase
{
    #[Test]
    public function creating_a_note_creates_an_action_via_its_observer()
    {
        $note = $this->make->note;

        $this->assertEquals('Note Added', $note->action->name);
    }
}
