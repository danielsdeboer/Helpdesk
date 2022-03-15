<?php

namespace Aviator\Helpdesk\Tests\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

abstract class CallAbstract
{
    use MakesHttpRequests;

    protected Application $app;

    public function __construct (Application $app)
    {
        $this->app = $app;
    }
}
