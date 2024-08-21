<?php

namespace Aviator\Helpdesk\Tests\Feature\Config;

use Aviator\Helpdesk\Tests\BKTestCase;
use Illuminate\Contracts\Routing\UrlGenerator;

class WithoutDomainTest extends BKTestCase
{
    /** @test */
    public function it_excludes_the_domain(): void
    {
        $url = resolve(UrlGenerator::class)->route('helpdesk.splash');

        $this->assertStringNotContainsString('dev.local', $url);
    }
}
