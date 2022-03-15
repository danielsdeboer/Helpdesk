<?php

namespace Aviator\Helpdesk\Tests\Support\Call;

use Aviator\Helpdesk\Tests\Support\Call\Admin\Teams;
use Aviator\Helpdesk\Tests\Support\CallAbstract;
use Illuminate\Contracts\Foundation\Application;

class Admin extends CallAbstract
{
    public Teams $teams;

    public function __construct (Application $app)
    {
        parent::__construct($app);

        $this->teams = new Teams($app);
    }
}
