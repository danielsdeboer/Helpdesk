<?php

return [
    /**
     * Define the user model
     */
    'userModel' => Aviator\Helpdesk\Tests\User::class,

    'tables' => [
        'users' => 'users',
        'tickets' => 'tickets',
        'generic_contents' => 'generic_contents',
        'actions' => 'actions',
        'assignments' => 'assignments'
    ],
];