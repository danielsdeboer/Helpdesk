<?php

namespace Aviator\Helpdesk\Tests\Integration\Users\Tickets;

use Aviator\Helpdesk\Tests\TestCase;

class ShowTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/permalink/';

    /**
     * @param null $id
     * @return string
     */
    protected function url ($id = null) : string
    {
        return $this->url . ($id ?: 1);
    }

    /** @test */
    public function guests_may_visit ()
    {
        $this->withoutErrorHandling();
        $ticket = $this->make->ticket;

        $response = $this->get($this->url($ticket->uuid));
        $response->assertSuccessful();
    }
}
