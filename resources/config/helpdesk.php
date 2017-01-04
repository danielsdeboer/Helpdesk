<?php

return [
    /**
     * Define the user model
     */
    'userModel' => \Aviator\Helpdesk\Tests\User::class,

    /**
     * The email address column on the user model. When we need to look up the supervisor's
     * user model for sending notifications.
     */
    'userModelEmailColumn' => 'email',

    'supervisor' => [
        'email' => 'supervisor@test.com',
    ],

    'tables' => [
        'users' => 'users',
        'tickets' => 'tickets',
        'generic_contents' => 'generic_contents',
        'actions' => 'actions',
        'assignments' => 'assignments',
        'due_dates' => 'due_dates',
        'internal_replies' => 'internal_replies',
        'external_replies' => 'external_replies',
        'pools' => 'pools',
        'pool_assignments' => 'pool_assignments',
        'closings' => 'closings',
        'openings' => 'openings',
        'notes' => 'notes',
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
                'route' => 'tickets.uuid.show'
            ],

            'replied' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Replied::class,
                'subject' => 'Your ticket has been replied to!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
                'route' => 'tickets.uuid.show'
            ],

            'closed' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Closed::class,
                'subject' => 'Your ticket has been closed.',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been marked as closed. You may press the button below to view the ticket and re-open it if desired.',
                'route' => 'tickets.uuid.show'
            ],
        ],

        'internal' => [
            'assignedToUser' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToUser::class,
                'subject' => 'A ticket has been assigned to you',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'tickets.show',
            ],

            'assignedToPool' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToPool::class,
                'subject' => 'A ticket has been assigned to your pool',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'tickets.show',
            ],

            'replied' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\Replied::class,
                'subject' => 'A ticket has been replied to',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'tickets.show',
            ],
        ]
    ],
];