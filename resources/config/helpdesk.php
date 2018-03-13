<?php

return [
    /*
     * Define the user model
     */
    'userModel' => \Aviator\Helpdesk\Tests\User::class,

    /*
     * The email address column on the user model. When we need to look up the supervisor's
     * user model for sending notifications.
     */
    'userModelEmailColumn' => 'email',

    /*
     * Callbacks. These are used to easily change what queries are executed when looking up
     * users, etc.
     */
    'callbacks' => [
        /*
         * The class to get the User filter closure from. Whatever class you specify here
         * will have a the 'getUserCallback()' method called on it. This closure enables
         * adding only a certain subset of user as a agents. The default implementation
         * looks for an is_internal field on the User, set to 1. You'll need to change
         * this to match your own implementation. If you use multiple guards or don't
         * care about this setting, set this key to null.
         */
        'user' => \Aviator\Helpdesk\Helpers\UserCallbackProvider::class,
    ],

    'tables' => [
        'users' => 'users',
        'tickets' => 'tickets',
        'agents' => 'agents',
        'agent_team' => 'agent_team',
        'generic_contents' => 'generic_contents',
        'actions' => 'actions',
        'assignments' => 'assignments',
        'due_dates' => 'due_dates',
        'replies' => 'replies',
        'teams' => 'teams',
        'team_assignments' => 'team_assignments',
        'closings' => 'closings',
        'openings' => 'openings',
        'notes' => 'notes',
        'collaborators' => 'collaborators',
    ],

    'header' => [
        'links' => []
    ],

    'from' => [
        'address' => 'noreply@test.com',
        'name' => 'Helpdesk Notifier',
    ],

    'notification' => \Aviator\Helpdesk\Notifications\Generic::class,

    'notifications' => [

        'opened' => [
            'subject' => 'Your ticket has been opened!',
            'greeting' => 'Hey there,',
            'line' => 'Your ticket has been opened. A member of our customer service staff will be in touch shortly.',
            'route' => 'helpdesk.tickets.public',
            'idType' => 'uuid',
        ],

        'agentReplied' => [
            'subject' => 'Your ticket has been replied to!',
            'greeting' => 'Hey there,',
            'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
            'route' => 'helpdesk.tickets.public',
            'idType' => 'uuid',
        ],

        'closed' => [
            'subject' => 'Your ticket has been closed.',
            'greeting' => 'Hey there,',
            'line' => 'Your ticket has been marked as closed. Click the button below to view the ticket and re-open it if desired.',
            'route' => 'helpdesk.tickets.public',
            'idType' => 'uuid',
        ],

        'assignedToAgent' => [
            'subject' => 'A ticket has been assigned to you',
            'greeting' => 'Hey there,',
            'line' => 'We have assigned you a ticket. Click the button below to view your ticket.',
            'route' => 'helpdesk.tickets.show',
            'idType' => 'id',
        ],

        'assignedToTeam' => [
            'subject' => 'A ticket has been assigned to your team',
            'greeting' => 'Hey there,',
            'line' => 'We have assigned a ticket to your team. Click the button below to view the ticket.',
            'route' => 'helpdesk.tickets.show',
            'idType' => 'id',
        ],

        'userReplied' => [
            'subject' => 'A ticket has been replied to',
            'greeting' => 'Hey there,',
            'line' => 'A user has replied to a ticket. Click the button below to view the ticket.',
            'route' => 'helpdesk.tickets.show',
            'idType' => 'id',
        ],

        'collaborator' => [
            'subject' => 'You\'ve been added as a collaborator',
            'greeting' => 'Hey there,',
            'line' => 'You have been added as a collaborator. Click the button below to view the ticket.',
            'route' => 'helpdesk.tickets.show',
            'idType' => 'id',
        ],
    ],

    'routes' => [
        'helpdesk' => [
            'prefix' => 'helpdesk',
        ],

        'admin' => [
            'prefix' => 'admin',
            'agents' => 'agents',
            'teams' => 'teams',
            'team-members' => [
                'add' => 'add',
                'remove' => 'remove',
            ],
        ],

        'dashboard' => [
            'prefix' => 'dashboard',
            'user' => 'user',
            'agent' => 'agent',
            'supervisor' => 'supervisor',
        ],

        'agents' => [
            'prefix' => 'agents',
            'tickets' => [
                'prefix' => 'tickets',
                'index' => '/',
            ],
        ],

        'tickets' => [
            'prefix' => 'tickets',
            'index' => [
                'route' => '/',
                'name' => 'index',
            ],
            'opened' => [
                'route' => 'open',
                'name' => 'opened',
            ],
            'closed' => [
                'route' => 'closed',
                'name' => 'closed',
            ],
            'show' => '{ticket}',

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
            'collab' => [
                'route' => 'collab/{ticket}',
                'name' => 'collab',
            ],
        ],
    ],

    'controllers' => [
        'admin' => [
            'agents' => '\Aviator\Helpdesk\Controllers\Admin\AgentsController',
            'teams' => '\Aviator\Helpdesk\Controllers\Admin\TeamsController',
            'team-members' => [
                'add' => '\Aviator\Helpdesk\Controllers\Admin\TeamMembersController@add',
                'remove' => '\Aviator\Helpdesk\Controllers\Admin\TeamMembersController@remove',
            ],
        ],
        'dashboard' => [
            'user' => '\Aviator\Helpdesk\Controllers\Dashboard\UserController@index',
            'agent' => '\Aviator\Helpdesk\Controllers\Dashboard\AgentController@index',
            'supervisor' => '\Aviator\Helpdesk\Controllers\Dashboard\SupervisorController@index',
        ],

        'tickets' => [
            'index' => '\Aviator\Helpdesk\Controllers\TicketsController@index',
            'opened' => '\Aviator\Helpdesk\Controllers\TicketsController@opened',
            'closed' => '\Aviator\Helpdesk\Controllers\TicketsController@closed',
            'show' => '\Aviator\Helpdesk\Controllers\TicketsController@show',
            'uuid' => [
                'show' => '\Aviator\Helpdesk\Controllers\Tickets\UuidController@show',
            ],
            'assign' => '\Aviator\Helpdesk\Controllers\Tickets\AssignmentController@create',
            'close' => '\Aviator\Helpdesk\Controllers\Tickets\ClosingController@create',
            'reply' => '\Aviator\Helpdesk\Controllers\Tickets\ReplyController@create',
            'note' => '\Aviator\Helpdesk\Controllers\Tickets\NoteController@create',
            'open' => '\Aviator\Helpdesk\Controllers\Tickets\OpeningController@create',
            'collab' => '\Aviator\Helpdesk\Controllers\Tickets\CollaboratorController@create',
        ],
    ],

    'footerText' => '<strong>Helpdesk</strong> by <a href="http://aviatorcreative.ca/">Aviator Creative</a>. Source code licensed <a href="https://opensource.org/licenses/mit-license.php">MIT</a>',

    'footerCopyrightText' => '&copy; 2017 Aviator Creative</a>',
];
