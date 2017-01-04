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
        'internal_replies' => 'internal_replies',
        'pools' => 'pools',
        'pool_assignments' => 'pool_assignments',
        'closings' => 'closings',
        'openings' => 'openings',
        'notes' => 'notes',
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
            'opened' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Opened::class,
                'subject' => 'Your ticket has been opened!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been opened. A member of our customer service staff will be in touch shortly.',
                'route' => ''
            ],

            'replied' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Replied::class,
                'subject' => 'Your ticket has been replied to!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
                'route' => ''
            ],

            'closed' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Closed::class,
                'subject' => 'Your ticket has been closed.',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been marked as closed. You may press the button below to view the ticket and re-open it if desired.',
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
            ],

            'assignedToPool' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToPool::class,
                'subject' => 'A ticket has been assigned to your pool',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => '',
            ]
        ]
    ],
];