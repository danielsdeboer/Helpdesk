<?php

namespace Aviator\Helpdesk\Observers;

use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Observers\Abstracts\AbstractObserver;

class DueDateObserver extends AbstractObserver
{
    /**
     * Listen to the created event.
     * @param DueDate $observed
     * @return void
     */
    public function created(DueDate $observed)
    {
        $this->createAction('due date added', $observed);
    }
}
