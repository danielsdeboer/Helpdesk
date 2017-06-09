<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;

class NoteObserver
{
    /**
     * Listen to the created event.
     *
     * @param  Note $observed
     * @return void
     */
    public function created(Note $observed)
    {
        $this->createAction($observed);
    }

    /**
     * Create the action.
     * @param  Note  $observed
     * @return void
     */
    protected function createAction(Note $observed)
    {
        $action = new Action;

        $action->name = 'Note Added';
        $action->subject_id = $observed->ticket_id;
        $action->subject_type = Ticket::class;
        $action->object_id = $observed->id;
        $action->object_type = Note::class;
        $action->save();
    }
}
