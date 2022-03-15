<?php

namespace Aviator\Helpdesk\Tests\Support;

use Aviator\Helpdesk\Tests\Support\Call\Admin;
use Illuminate\Contracts\Foundation\Application;

class Call extends CallAbstract
{
    public Admin $admin;

    public function __construct (Application $app)
    {
        parent::__construct($app);

        $this->admin = new Admin($app);
    }
}
