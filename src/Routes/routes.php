<?php

Route::group([
    'as' => config('helpdesk.routes.helpdesk.prefix') . '.',
    'prefix' => config('helpdesk.routes.dashboard.prefix'),
], function() {

    Route::group([
        'as' => config('helpdesk.routes.dashboard.prefix') . '.',
        'prefix' => config('helpdesk.routes.dashboard.prefix'),
    ], function() {

        Route::get('/', function() {
            return;
        })->middleware(\Aviator\Helpdesk\Middleware\DashboardRouter::class)->name('router');

        Route::get(config('helpdesk.routes.dashboard.user'), function() {
            return 'user-dashboard';
        })->name(config('helpdesk.routes.dashboard.user'));

        Route::get(config('helpdesk.routes.dashboard.agent'), function() {
            return 'agent-dashboard';
        })->name(config('helpdesk.routes.dashboard.agent'));

        Route::get(config('helpdesk.routes.dashboard.supervisor'), function() {
            return 'supervisor-dashboard';
        })->name(config('helpdesk.routes.dashboard.supervisor'));
    });

});

