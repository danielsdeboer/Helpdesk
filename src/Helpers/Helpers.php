<?php

namespace Aviator\Helpdesk\Helpers;

use Aviator\Helpdesk\Models\Action;
use Illuminate\Database\Eloquent\Builder;

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

        return '(deleted user)';
    }

    /**
     * Get a callback to filter users.
     * @return \Closure
     */
    public function getUserCallback ()
    {
        return function (Builder $query) {
            $query->where('is_internal', 1);
        };
    }
}
