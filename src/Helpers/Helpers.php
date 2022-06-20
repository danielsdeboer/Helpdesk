<?php

namespace Aviator\Helpdesk\Helpers;

use Aviator\Helpdesk\Models\Action;

class Helpers
{
    /**
     * Get the name of the user or agent who created a ticket.
     * @param Action $action
     * @return string
     */
    public static function actionCreator(Action $action)
    {
        if ($action->object->agent) {
            return $action->object->agent->user->name;
        }

        if ($action->object->user) {
            return $action->object->user->name;
        }

        if ($action->object->agent_id === null) {
            return 'System Process';
        }

        return '(deleted user)';
    }
}
