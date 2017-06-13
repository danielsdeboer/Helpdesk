<?php

// Helpdesk Group
Route::group([
    'as' => config('helpdesk.routes.helpdesk.prefix') . '.',
    'prefix' => config('helpdesk.routes.helpdesk.prefix'),
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
        'as' => config('helpdesk.routes.admin.prefix') . '.',
        'prefix' => config('helpdesk.routes.admin.prefix'),
    ], function () {

        // Team Members Group
        Route::group([
            'as' => 'team-members.',
            'prefix' => 'team-members',
        ], function () {
            Route::post(
                config('helpdesk.routes.admin.team-members.add'),
                config('helpdesk.controllers.admin.team-members.add')
            )->name(config('helpdesk.routes.admin.team-members.add'));

            Route::post(
                config('helpdesk.routes.admin.team-members.remove'),
                config('helpdesk.controllers.admin.team-members.remove')
            )->name(config('helpdesk.routes.admin.team-members.remove'));
        });

        Route::resource(
            config('helpdesk.routes.admin.agents'),
            config('helpdesk.controllers.admin.agents'),
            [
                'except' => [
                    'create',
                    'edit',
                    'update',
                ],
            ]
        );

        Route::resource(
            config('helpdesk.routes.admin.teams'),
            config('helpdesk.controllers.admin.teams'),
            [
                'except' => [
                    'create',
                    'edit',
                ],
            ]
        );
    });

    // Dashboard Group
    Route::group([
        'as' => config('helpdesk.routes.dashboard.prefix') . '.',
        'prefix' => config('helpdesk.routes.dashboard.prefix'),
    ], function () {
        Route::get('/', '\Aviator\Helpdesk\Controllers\PublicController@doNothing')
            ->middleware(\Aviator\Helpdesk\Middleware\DashboardRouter::class)
            ->name('router');

        Route::get(
            config('helpdesk.routes.dashboard.user'),
            config('helpdesk.controllers.dashboard.user')
        )->name(config('helpdesk.routes.dashboard.user'));

        Route::get(
            config('helpdesk.routes.dashboard.agent'),
            config('helpdesk.controllers.dashboard.agent')
        )->name(config('helpdesk.routes.dashboard.agent'));

        Route::get(
            config('helpdesk.routes.dashboard.supervisor'),
            config('helpdesk.controllers.dashboard.supervisor')
        )->name(config('helpdesk.routes.dashboard.supervisor'));
    });

    // Tickets Group
    Route::group([
        'as' => config('helpdesk.routes.tickets.prefix') . '.',
        'prefix' => config('helpdesk.routes.tickets.prefix'),
    ], function () {

        // Index
        Route::get(
            config('helpdesk.routes.tickets.index.route'),
            config('helpdesk.controllers.tickets.index')
        )->name(config('helpdesk.routes.tickets.index.name'));

        // Opened index
        Route::get(
            config('helpdesk.routes.tickets.opened.route'),
            config('helpdesk.controllers.tickets.opened')
        )->name(config('helpdesk.routes.tickets.opened.name'));

        // Closed index
        Route::get(
            config('helpdesk.routes.tickets.closed.route'),
            config('helpdesk.controllers.tickets.closed')
        )->name(config('helpdesk.routes.tickets.closed.name'));

        // Show
        Route::get(
            config('helpdesk.routes.tickets.show.route'),
            config('helpdesk.controllers.tickets.show')
        )->name(config('helpdesk.routes.tickets.show.name'));

        // Show by uuid
        Route::get(
            config('helpdesk.routes.tickets.uuid.route'),
            config('helpdesk.controllers.tickets.uuid.show')
        )->name(config('helpdesk.routes.tickets.uuid.name'));

        // Assign
        Route::post(
            config('helpdesk.routes.tickets.assign.route'),
            config('helpdesk.controllers.tickets.assign')
        )->name(config('helpdesk.routes.tickets.assign.name'));

        // Reply
        Route::post(
            config('helpdesk.routes.tickets.reply.route'),
            config('helpdesk.controllers.tickets.reply')
        )->name(config('helpdesk.routes.tickets.reply.name'));

        // Close
        Route::post(
            config('helpdesk.routes.tickets.close.route'),
            config('helpdesk.controllers.tickets.close')
        )->name(config('helpdesk.routes.tickets.close.name'));

        // Note
        Route::post(
            config('helpdesk.routes.tickets.note.route'),
            config('helpdesk.controllers.tickets.note')
        )->name(config('helpdesk.routes.tickets.note.name'));

        // Note
        Route::post(
            config('helpdesk.routes.tickets.open.route'),
            config('helpdesk.controllers.tickets.open')
        )->name(config('helpdesk.routes.tickets.open.name'));
    });
});
