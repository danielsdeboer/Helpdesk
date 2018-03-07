<?php

namespace Aviator\Helpdesk\Tests\fixtures;

class LevelOne
{
    /** @var LevelTwo */
    public $levelTwo;

    public function __construct()
    {
        $this->levelTwo = new LevelTwo;
    }
}
