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
        'agents' => 'agents',
        'agent_pool' => 'agent_pool',
        'generic_contents' => 'generic_contents',
        'actions' => 'actions',
        'assignments' => 'assignments',
        'due_dates' => 'due_dates',
        'replies' => 'replies',
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
                'route' => 'helpdesk.tickets.public'
            ],

            'replied' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Replied::class,
                'subject' => 'Your ticket has been replied to!',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
                'route' => 'helpdesk.tickets.public'
            ],

            'closed' => [
                'class' => \Aviator\Helpdesk\Notifications\External\Closed::class,
                'subject' => 'Your ticket has been closed.',
                'greeting' => 'Hey there.',
                'line' => 'Your ticket has been marked as closed. You may press the button below to view the ticket and re-open it if desired.',
                'route' => 'helpdesk.tickets.public'
            ],
        ],

        'internal' => [
            'assignedToAgent' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToAgent::class,
                'subject' => 'A ticket has been assigned to you',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'helpdesk.tickets.show',
            ],

            'assignedToPool' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\AssignedToPool::class,
                'subject' => 'A ticket has been assigned to your pool',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'helpdesk.tickets.show',
            ],

            'replied' => [
                'class' => \Aviator\Helpdesk\Notifications\Internal\Replied::class,
                'subject' => 'A ticket has been replied to',
                'greeting' => 'Hey there.',
                'line' => '',
                'route' => 'helpdesk.tickets.show',
            ],
        ]
    ],

    'routes' => [
        'helpdesk' => [
            'prefix' => 'helpdesk',
        ],

        'dashboard' => [
            'prefix' => 'dashboard',
            'user' => 'user',
            'agent' => 'agent',
            'supervisor' => 'supervisor',
        ],

        'tickets' => [
            'prefix' => 'tickets',
            'index' => [
                'route' => '/',
                'name' => 'index',
            ],
            'show' => [
                'route' => '{ticket}',
                'name' => 'show'
            ],
            'uuid' => [
                'route' => 'public/{uuid}',
                'name' => 'public',
            ],
            'assign' => [
                'route' => 'assign/{ticket}',
                'name' => 'assign',
            ],
            'close' => [
                'route' => 'close/{ticket}',
                'name' => 'close',
            ],
            'reply' => [
                'route' => 'reply/{ticket}',
                'name' => 'reply',
            ],
            'note' => [
                'route' => 'note/{ticket}',
                'name' => 'note',
            ],
            'open' => [
                'route' => 'open/{ticket}',
                'name' => 'open',
            ],
        ],
    ],

    'controllers' => [
        'dashboard' => [
            'user' => '\Aviator\Helpdesk\Controllers\Dashboard\UserController@index',
            'agent' => '\Aviator\Helpdesk\Controllers\Dashboard\AgentController@index',
            'supervisor' => '\Aviator\Helpdesk\Controllers\Dashboard\SupervisorController@index',
        ],

        'tickets' => [
            'index' => '\Aviator\Helpdesk\Controllers\TicketsController@index',
            'show' => '\Aviator\Helpdesk\Controllers\TicketsController@show',
            'uuid' => [
                'show' => '\Aviator\Helpdesk\Controllers\Tickets\UuidController@show',
            ],
            'assign' => '\Aviator\Helpdesk\Controllers\Tickets\AssignmentController@create',
            'close' => '\Aviator\Helpdesk\Controllers\Tickets\ClosingController@create',
            'reply' => '\Aviator\Helpdesk\Controllers\Tickets\ReplyController@create',
            'note' => '\Aviator\Helpdesk\Controllers\Tickets\NoteController@create',
            'open' => '\Aviator\Helpdesk\Controllers\Tickets\OpeningController@create',
        ]
    ],
];