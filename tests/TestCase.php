<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Database\Migrations\CreateHelpdeskTables;
use Aviator\Database\Migrations\CreateUsersTable;
use Aviator\Helpdesk\HelpdeskServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../resources/factories');

        $this->setUpDatabase();

        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');

        $this->createSupervisorUser();

        Notification::fake();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            HelpdeskServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', 'true');
        $app['config']->set('app.key', 'base64:2+SetJaztC7g0a1sSF81LYsDasiWymO6tp8yVv6KGrA=');
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUpDatabase()
    {
        // Create testing database fixtures
        include_once __DIR__ . '/../database/migrations/2017_01_01_000000_create_users_table.php';
        (new CreateUsersTable())->up();
    }

    /**
     * Create the supervisor user. This is necessary as the supervisor user
     * is the fallback for notifications where an assignment or pool assignment
     * are not set.
     * @return void
     */
    protected function createSupervisorUser()
    {
        $userModel = config('helpdesk.userModel');

        $userModel::create([
            'name' => 'Super Visor',
            'email' => config('helpdesk.supervisor.email')
        ]);
    }
}