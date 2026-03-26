<?php

namespace Aviator\Helpdesk\Tests\Feature\Config;

use Aviator\Helpdesk\Tests\BKTestCase;
use Illuminate\Contracts\Routing\UrlGenerator;
use PHPUnit\Framework\Attributes\Test;

class WithDomainTest extends BKTestCase
{
    public function setUp(): void
    {
        $this->domain = 'dev.local';

        parent::setUp();
    }

    #[Test]
    public function it_includes_the_domain(): void
    {
        $url = resolve(UrlGenerator::class)->route('helpdesk.splash');

        $this->assertStringContainsString('dev.local', $url);
    }
}
