<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class NoteObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param Note $observed
     * @return void
     */
    public function created (Note $observed)
    {
        $this->createAction('note added', $observed);
    }
}
