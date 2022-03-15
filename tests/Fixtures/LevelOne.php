<?php

namespace Aviator\Helpdesk\Tests\Fixtures;

class LevelOne
{
    /** @var LevelTwo */
    public $levelTwo;

    public function __construct()
    {
        $this->levelTwo = new LevelTwo;
    }
}
