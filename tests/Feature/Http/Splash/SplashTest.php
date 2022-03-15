<?php

namespace Aviator\Helpdesk\Tests\Feature\Http\Splash;

use Aviator\Helpdesk\Tests\BKTestCase;

class SplashTest extends BKTestCase
{
    /** @test */
    public function the_splash_exists()
    {
        $this->visit('/helpdesk')
            ->see('<i class="material-icons">chat</i>')
            ->see('<h1 class="title">Helpdesk</h1>')
            ->see('<h2 class="subtitle">Build <strong>great relationships</strong>.</h2>');
    }
}
