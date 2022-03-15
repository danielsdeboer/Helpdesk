<?php

namespace Aviator\Helpdesk\Tests\Support;

class LevelOne
{
    /** @var LevelTwo */
    public $levelTwo;

    public function __construct()
    {
        $this->levelTwo = new LevelTwo;
    }
}
