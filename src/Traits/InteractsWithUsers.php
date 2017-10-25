<?php

namespace Aviator\Helpdesk\Traits;

trait InteractsWithUsers
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $userModelName;

    /** @var string */
    protected $userModelEmailColumn;

    /**
     * Set the user config model and email column.
     * @return void
     */
    public function setUserConfig ()
    {
        $this->userModelName = config('helpdesk.userModel');
        $this->userModelEmailColumn = config('helpdesk.userModelEmailColumn');
    }

    /**
     * Get a list of users. If a callback is set, filter with that. Otherwise get all users.
     * @return mixed
     */
    protected function fetchUsers()
    {
        if (! config('helpdesk.callbacks.user')) {
            return $this->fetchAllUsers();
        }

        return $this->fetchFilteredUsers();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function fetchAllUsers ()
    {
        return $this->userModelName::all();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function fetchFilteredUsers ()
    {
        $class = config('helpdesk.callbacks.user');
        /** @var \Aviator\Helpdesk\Interfaces\HasUserCallback $callback */
        $callback = new $class;
        $callback = $callback->getUserCallback();

        return $this->userModelName::query()->where($callback)->get();
    }
}
