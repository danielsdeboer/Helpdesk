<?php

// Helpdesk Group
Route::group([
    'as' => config('helpdesk.routes.helpdesk.prefix') . '.',
    'prefix' => config('helpdesk.routes.helpdesk.prefix'),
    'middleware' => 'web',
], function() {

    Route::get('admin', function() {
        return redirect( route('helpdesk.admin.agents.index') );
    })->name('admin')->middleware(['auth', 'helpdesk.supervisors']);

    // Admin Group
    Route::group([
        'as' => config('helpdesk.routes.admin.prefix') . '.',
        'prefix' => config('helpdesk.routes.admin.prefix'),
    ], function() {

        // Team Members Group
        Route::group([
            'as' => 'team-members.',
            'prefix' => 'team-members',
        ], function() {
            Route::post(
                config('helpdesk.routes.admin.team-members.store'),
                config('helpdesk.controllers.admin.team-members.store')
            )->name(config('helpdesk.routes.admin.team-members.store'));
        });

        Route::resource(
            config('helpdesk.routes.admin.agents'),
            config('helpdesk.controllers.admin.agents')
        );

        Route::resource(
            config('helpdesk.routes.admin.teams'),
            config('helpdesk.controllers.admin.teams')
        );
    });

    // Dashboard Group
    Route::group([
        'as' => config('helpdesk.routes.dashboard.prefix') . '.',
        'prefix' => config('helpdesk.routes.dashboard.prefix'),
    ], function() {

        Route::get('/', function() {
            return;
        })->middleware(\Aviator\Helpdesk\Middleware\DashboardRouter::class)->name('router');

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
    ], function() {

        // Index
        Route::get(
            config('helpdesk.routes.tickets.index.route'),
            config('helpdesk.controllers.tickets.index')
        )->name(config('helpdesk.routes.tickets.index.name'));

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

