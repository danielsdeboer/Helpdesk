<?php
/**
 * Created by PhpStorm.
 * User: ddeboer
 * Date: 9/1/2017
 * Time: 2:08 PM.
 */

namespace Aviator\Helpdesk\Traits;

use Aviator\Helpdesk\Models\Agent;

trait FetchesAuthorizedAgent
{
    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    protected function fetchAuthorizedAgent()
    {
        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        $agent = Agent::query()
            ->where('user_id', auth()->user()->id)
            ->first();

        return $agent;
    }
}
