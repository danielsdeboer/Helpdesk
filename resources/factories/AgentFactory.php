<?php

use Aviator\Helpdesk\Models\Agent;

$factory->define(Agent::class, function () {
    return [
        'user_id' => factory(config('helpdesk.userModel'))->create()->id,
        'is_super' => 0,
    ];
});

$factory->state(Agent::class, 'isSuper', function () {
    return [
        'is_super' => 1,
    ];
});
