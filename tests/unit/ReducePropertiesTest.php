<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Tests\fixtures\LevelOne;

class ReducePropertiesTest extends BKTestCase
{
    /** @test */
    public function it_takes_an_object_and_a_dot_notated_path_and_gets_the_properties ()
    {
        $class = new LevelOne;
        $result = reduceProperties($class, 'levelTwo.levelThree.levelFour');

        $this->assertSame('testing', $result);
    }

    /** @test */
    public function if_it_receives_a_null_value_from_the_previous_iteration_it_returns_null ()
    {
        $class = new LevelOne;
        $result = reduceProperties($class, 'levelTwo.fails.somethingElse');

        $this->assertSame(null, $result);
    }
}
