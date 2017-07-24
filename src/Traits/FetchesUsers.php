<?php

namespace Aviator\Helpdesk\Traits;

trait FetchesUsers
{
    protected function fetchUsers()
    {
        $userModel = config('helpdesk.userModel');

        if (! config('helpdesk.callbacks.user')) {
            return $userModel::all();
        }

        return $userModel::where(config('helpdesk.callbacks.user'))->get();
    }
}
