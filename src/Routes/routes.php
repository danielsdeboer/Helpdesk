<?php

// Helpdesk Group
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'helpdesk.',
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
        'as' => 'admin.',
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
            )->name('add');

            Route::post(
                config('helpdesk.routes.admin.team-members.remove'),
                config('helpdesk.controllers.admin.team-members.remove')
            )->name('remove');
        });

        Route::resource(
            config('helpdesk.routes.admin.agents'),
            config('helpdesk.controllers.admin.agents'),
            ['except' => [
                'create',
                'edit',
                'update',
            ]]
        );

        Route::resource(
            config('helpdesk.routes.admin.teams'),
            config('helpdesk.controllers.admin.teams'),
            ['except' => [
                'create',
                'edit',
            ]]
        );
    });

    // Dashboard Group
    Route::group([
        'as' => 'dashboard.',
        'prefix' => config('helpdesk.routes.dashboard.prefix'),
    ], function () {
        Route::get('/', '\Aviator\Helpdesk\Controllers\PublicController@doNothing')
            ->middleware(\Aviator\Helpdesk\Middleware\DashboardRedirector::class)
            ->name('router');

        Route::get(
            config('helpdesk.routes.dashboard.user'),
            config('helpdesk.controllers.dashboard.user')
        )->name('user');

        Route::get(
            config('helpdesk.routes.dashboard.agent'),
            config('helpdesk.controllers.dashboard.agent')
        )->name('agent');

        Route::get(
            config('helpdesk.routes.dashboard.supervisor'),
            config('helpdesk.controllers.dashboard.supervisor')
        )->name('supervisor');
    });

    // Tickets Group
    Route::group([
        'as' => 'tickets.',
        'prefix' => config('helpdesk.routes.tickets.prefix'),
    ], function () {

        /*
         * The tickets index. This index provides a list of open and closed
         * tickets. It is meant for users only. Agents will be redirected
         * to the agents tickets page (below).
         */
        Route::get(
            config('helpdesk.routes.tickets.index'),
            'Aviator\Helpdesk\Controllers\TicketsController@index'
        )->name('index');

        // Opened index
        Route::get(
            config('helpdesk.routes.tickets.opened'),
            'Aviator\Helpdesk\Controllers\OpenTicketsController@index'
        )->name('opened.index');

        // Closed index
        Route::get(
            config('helpdesk.routes.tickets.closed'),
            'Aviator\Helpdesk\Controllers\ClosedTicketsController@index'
        )->name('closed.index');

        // Show
        Route::get(
            config('helpdesk.routes.tickets.show'),
            'Aviator\Helpdesk\Controllers\Users\TicketsController@show'
        )->name('show');

        // Show by uuid
        Route::get(
            config('helpdesk.routes.tickets.uuid.route'),
            config('helpdesk.controllers.tickets.uuid.show')
        )->name('public');

        // Assign
        Route::post(
            config('helpdesk.routes.tickets.assign.route'),
            config('helpdesk.controllers.tickets.assign')
        )->name('assign');

        // Reply
        Route::post(
            config('helpdesk.routes.tickets.reply.route'),
            config('helpdesk.controllers.tickets.reply')
        )->name('reply');

        // Close
        Route::post(
            config('helpdesk.routes.tickets.close.route'),
            config('helpdesk.controllers.tickets.close')
        )->name('close');

        // Note
        Route::post(
            config('helpdesk.routes.tickets.note.route'),
            config('helpdesk.controllers.tickets.note')
        )->name('note');

        // Note
        Route::post(
            config('helpdesk.routes.tickets.open.route'),
            config('helpdesk.controllers.tickets.open')
        )->name('open');

        // Add Collaborator
        Route::post(
            config('helpdesk.routes.tickets.collab.route'),
            config('helpdesk.controllers.tickets.collab')
        )->name('collab');
    });
});
