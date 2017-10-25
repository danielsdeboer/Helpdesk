<?php

namespace Aviator\Helpdesk\Interfaces;

use Closure;

interface HasUserCallback
{
    public function getUserCallback () : Closure;
}
