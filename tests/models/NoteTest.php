<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Note;

class NoteTest extends TestCase
{
    /**
     * @group model
     * @group model.note
     * @test
     */
    public function creating_a_note_creates_an_action_via_its_observer()
    {
        $note = factory(Note::class)->create();

        $this->assertEquals('Note Added', $note->action->name);
    }
}
