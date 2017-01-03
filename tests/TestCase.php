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

        $this->withFactories(
            realpath(dirname(__DIR__).'/database/factories')
        );

        $this->setUpDatabase();

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
        include_once __DIR__ . '/../database/migrations/create_users_table.php';
        (new CreateUsersTable())->up();

        // Create Helpdesk tables
        include_once __DIR__ . '/../resources/migrations/create_helpdesk_tables.php';
        (new CreateHelpdeskTables())->up();
    }
}