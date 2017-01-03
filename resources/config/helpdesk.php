<?php

return [
    /**
     * Define the user model
     */
    'userModel' => \Aviator\Helpdesk\Tests\User::class,

    'tables' => [
        'users' => 'users',
        'tickets' => 'tickets',
        'generic_contents' => 'generic_contents',
        'actions' => 'actions',
        'assignments' => 'assignments',
        'due_dates' => 'due_dates',
        'emails' => 'emails',
    ],

    'supervisor' => [
        'email' => 'supervisor@test.com',
    ],

    'from' => [
        'address' => 'noreply@test.com',
        'name' => 'Helpdesk Notifier',
    ],

    'notifications' => [
        'external' => [
            'created' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Created::class,
                'subject' => 'Your ticket has been placed!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been created. A member of our customer service staff will be in touch shortly.',
                'route' => ''
            ],

            'emailed' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Emailed::class,
                'subject' => 'Your ticket has been replied to!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
                'route' => ''
            ],
        ],

        'internal' => [
            'assignedToUser' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToUser::class,
                'subject' => 'A ticket has been assigned to you',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => '',
            ]
        ]
    ],
];