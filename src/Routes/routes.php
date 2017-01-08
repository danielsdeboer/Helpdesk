<?php

Route::group([
    'as' => config('helpdesk.routes.helpdesk.prefix') . '.',
    'prefix' => config('helpdesk.routes.helpdesk.prefix'),
    'middleware' => 'web',
], function() {

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

        Route::get(config('helpdesk.routes.dashboard.supervisor'), function() {
            return 'supervisor-dashboard';
        })->name(config('helpdesk.routes.dashboard.supervisor'));
    });

});

