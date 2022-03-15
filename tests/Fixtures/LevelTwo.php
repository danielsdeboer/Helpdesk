<?php

namespace Aviator\Helpdesk\Tests\Fixtures;

class LevelTwo
{
    /** @var LevelThree */
    public $levelThree;

    /** @var null */
    public $fails;

    public function __construct()
    {
        $this->levelThree = new LevelThree;
    }
}
