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

    /**
     * List of users that will be ignored. They can do anything a typical customer can,
     * however, no notifications from them will be received and only super users will
     * be able to see their tickets.
     */
    'ignored' => [

    ],

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
        'links' => [],
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
            'route' => 'helpdesk.tickets.permalink.show',
            'idType' => 'uuid',
        ],

        'agentReplied' => [
            'subject' => 'Your ticket has been replied to!',
            'greeting' => 'Hey there,',
            'line' => 'Your ticket has been replied to. Click the button below to review the reply.',
            'route' => 'helpdesk.tickets.permalink.show',
            'idType' => 'uuid',
        ],

        'closed' => [
            'subject' => 'Your ticket has been closed.',
            'greeting' => 'Hey there,',
            'line' => 'Your ticket has been marked as closed. Click the button below to view the ticket and re-open it if desired.',
            'route' => 'helpdesk.tickets.permalink.show',
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
            'disabled' => 'disabled',
            'team-members' => [
                'add' => 'add',
                'remove' => 'remove',
                'make-team-lead' => 'make-team-lead',
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
                'show' => '{ticket}',
            ],
        ],

        'tickets' => [
            'prefix' => 'tickets',
            'index' => '/',
            'opened' => 'open',
            'closed' => 'closed',
            'ignored' => 'ignored',
            'show' => '{ticket}',
            'permalink' => 'permalink/{uuid}',
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

    'footerText' => '
        <strong>Helpdesk</strong> by <a href="http://aviatorcreative.ca/">Aviator Creative</a>.
        Source code licensed <a href="https://opensource.org/licenses/mit-license.php">MIT</a>
    ',

    'footerCopyrightText' => '&copy; 2017 Aviator Creative</a>',
];
