<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\Fixtures\Get;
use Aviator\Helpdesk\Tests\Fixtures\Make;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use Aviator\Helpdesk\HelpdeskServiceProvider;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Aviator\Database\Migrations\CreateUsersTable;
use Orchestra\Testbench\BrowserKit\TestCase as OrchestraBrowserKit;
use PHPUnit\Framework\Assert;

abstract class BKTestCase extends OrchestraBrowserKit
{
    /** @var \Aviator\Helpdesk\Tests\Fixtures\Make */
    protected $make;

    /** @var \Aviator\Helpdesk\Tests\Fixtures\Get */
    protected $get;

    /** @var array */
    protected $supers = [
        [
            'name' => 'Super Visor',
            'email' => 'supervisor@test.com',
        ],
        [
            'name' => 'Other Visor',
            'email' => 'some.other@email.com',
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../resources/factories');

        $this->setUpDatabase();

        $this->artisan('migrate', [
            '--database'    => 'testing',
        ]);

        $this->createSupers();

        Notification::fake();

        $this->make = new Make();
        $this->get = new Get();

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue(
                $this->contains($value),
                'Failed asserting that the collection contains the given value.'
            );
        });
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

        if (isset($GLOBALS['altdb']) && $GLOBALS['altdb'] === true) {
            $this->setAlternateTablesInConfig($app);
        }

        Route::get('login', function () {
            //
        })->name('login');
    }

    protected function setUpDatabase()
    {
        // Create testing database fixtures
        include_once __DIR__ . '/../database/migrations/2017_01_01_000000_create_users_table.php';
        (new CreateUsersTable())->up();
    }

    /**
     * Create the supervisor user. This is necessary as the supervisor user
     * is the fallback for notifications where an assignment or team assignment
     * are not set.
     * @return void
     */
    protected function createSupers()
    {
        foreach ($this->supers as $super) {
            /** @var \Aviator\Helpdesk\Tests\User $user */
            $user = User::query()->create([
                'name' => $super['name'],
                'email' => $super['email'],
            ]);

            Agent::query()->create([
               'user_id' => $user->id,
                'is_super' => 1,
            ]);
        }
    }

    /**
     * Set alternate table names for testing that the database names
     * are properly variable everywhere.
     * @param $app
     * @return void
     */
    protected function setAlternateTablesInConfig($app)
    {
        $prefix = 'hd_';

        $app->config->set('helpdesk.tables.users', 'users');
        $app->config->set('helpdesk.tables.tickets', $prefix . 'tickets');
        $app->config->set('helpdesk.tables.agents', $prefix . 'agents');
        $app->config->set('helpdesk.tables.agent_team', $prefix . 'agent_team');
        $app->config->set('helpdesk.tables.actions', $prefix . 'actions');
        $app->config->set('helpdesk.tables.generic_contents', $prefix . 'generic_contents');
        $app->config->set('helpdesk.tables.assignments', $prefix . 'assignments');
        $app->config->set('helpdesk.tables.due_dates', $prefix . 'due_dates');
        $app->config->set('helpdesk.tables.replies', $prefix . 'replies');
        $app->config->set('helpdesk.tables.teams', $prefix . 'teams');
        $app->config->set('helpdesk.tables.team_assignments', $prefix . 'team_assignments');
        $app->config->set('helpdesk.tables.closings', $prefix . 'closings');
        $app->config->set('helpdesk.tables.openings', $prefix . 'openings');
        $app->config->set('helpdesk.tables.notes', $prefix . 'notes');
        $app->config->set('helpdesk.tables.collaborators', $prefix . 'collaborators');
    }

    protected function withoutErrorHandling ()
    {
        app()->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct()
            {
            }

            public function report(\Exception $e)
            {
            }

            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });
    }
}
