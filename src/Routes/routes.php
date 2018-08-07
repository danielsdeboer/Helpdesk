<?php

// Helpdesk Group
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'helpdesk.',
    'prefix' => hd_route('helpdesk.prefix'),
    'middleware' => 'web',
], function () {

    // Helpdesk splash page
    Route::get('/', '\Aviator\Helpdesk\Controllers\PublicController@splash');

    // Helpdesk admin redirect
    Route::get('admin', '\Aviator\Helpdesk\Controllers\PublicController@redirectToAdmin')
        ->name('admin')
        ->middleware(['auth', 'helpdesk.supervisors']);

    // Admin Group
    Route::group([
        'as' => 'admin.',
        'prefix' => hd_route('admin.prefix'),
    ], function () {

        // Team Members Group
        Route::group([
            'as' => 'team-members.',
            'prefix' => 'team-members',
        ], function () {
            Route::post(
                hd_route('admin.team-members.add'),
                '\Aviator\Helpdesk\Controllers\Admin\TeamMembersController@add'
            )->name('add');

            Route::post(
                hd_route('admin.team-members.remove'),
                '\Aviator\Helpdesk\Controllers\Admin\TeamMembersController@remove'
            )->name('remove');

            Route::post(
                hd_route('admin.team-members.make-team-lead'),
                '\Aviator\Helpdesk\Controllers\Admin\TeamMembersController@makeTeamLead'
            )->name('make-team-lead');
        });

        Route::resource(
            hd_route('admin.agents'),
            '\Aviator\Helpdesk\Controllers\Admin\AgentsController',
            ['except' => ['create', 'edit', 'update']]
        );

        Route::resource(
            hd_route('admin.teams'),
            '\Aviator\Helpdesk\Controllers\Admin\TeamsController',
            ['except' => ['create', 'edit']]
        );
    });

    // Dashboard Group
    Route::group([
        'as' => 'dashboard.',
        'prefix' => hd_route('dashboard.prefix'),
    ], function () {
        Route::get('/', '\Aviator\Helpdesk\Controllers\PublicController@doNothing')
            ->middleware(\Aviator\Helpdesk\Middleware\DashboardRedirector::class)
            ->name('router');

        Route::get(
            hd_route('dashboard.user'),
            '\Aviator\Helpdesk\Controllers\Dashboard\UserController@index'
        )->name('user');

        Route::get(
            hd_route('dashboard.agent'),
            '\Aviator\Helpdesk\Controllers\Dashboard\AgentController@index'
        )->name('agent');

        Route::get(
            hd_route('dashboard.supervisor'),
            '\Aviator\Helpdesk\Controllers\Dashboard\SupervisorController@index'
        )->name('supervisor');
    });

    // Tickets Group
    Route::group([
        'as' => 'tickets.',
        'prefix' => hd_route('tickets.prefix'),
    ], function () {
        /*
         * The tickets index. This index provides a list of open and closed
         * tickets with links to paginated open/closed indexes.
         */
        Route::get(
            hd_route('tickets.index'),
            'Aviator\Helpdesk\Controllers\TicketsController@index'
        )->name('index');

        /*
         * Paginated index of open tickets.
         */
        Route::get(
            hd_route('tickets.opened'),
            'Aviator\Helpdesk\Controllers\OpenTicketsController@index'
        )->name('opened.index');

        /*
         * Paginated index of closed tickets.
         */
        Route::get(
            hd_route('tickets.closed'),
            'Aviator\Helpdesk\Controllers\ClosedTicketsController@index'
        )->name('closed.index');

        /*
         * Private (authorized) view for a single ticket.
         */
        Route::get(
            hd_route('tickets.show'),
            'Aviator\Helpdesk\Controllers\TicketsController@show'
        )->name('show');

        /*
         * Public (permalink, read-only) view for a single ticket.
         */
        Route::get(
            hd_route('tickets.permalink'),
            '\Aviator\Helpdesk\Controllers\Tickets\PermalinkController@show'
        )->name('permalink.show');

        /*
         * Create an assignment.
         */
        Route::post(
            hd_route('tickets.assign.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\AssignmentController@create'
        )->name('assign');

        /*
         * Create a reply.
         */
        Route::post(
            hd_route('tickets.reply.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\ReplyController@create'
        )->name('reply');

        /*
         * Create a closing.
         */
        Route::post(
            hd_route('tickets.close.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\ClosingController@create'
        )->name('close');

        /*
         * Create a note.
         */
        Route::post(
            hd_route('tickets.note.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\NoteController@create'
        )->name('note');

        /*
         * Create an opening.
         */
        Route::post(
            hd_route('tickets.open.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\OpeningController@create'
        )->name('open');

        /*
         * Create a collaborator.
         */
        Route::post(
            hd_route('tickets.collab.route'),
            '\Aviator\Helpdesk\Controllers\Tickets\CollaboratorController@create'
        )->name('collab');
    });
});
