<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Database\Migrations\CreateUsersTable;
use Aviator\Helpdesk\HelpdeskServiceProvider;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\Support\Get;
use Aviator\Helpdesk\Tests\Support\Make;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\BrowserKit\TestCase as OrchestraBrowserKit;
use PHPUnit\Framework\Assert;
use Throwable;

abstract class BKTestCase extends OrchestraBrowserKit
{
    protected Make $make;

    protected Get $get;

    protected array $supers = [
        [
            'name' => 'Super Visor',
            'email' => 'supervisor@test.com',
        ],
        [
            'name' => 'Other Visor',
            'email' => 'some.other@email.com',
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->getEnvironmentSetUp(resolve(App::class));

        $this->withFactories(__DIR__ . '/../resources/factories');

        $this->setUpDatabase();

        $this->artisan('migrate', [
            '--database' => 'testing',
        ]);

        $this->createSupers();

        Notification::fake();

        $this->make = new Make();
        $this->get = new Get();

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue(
                $this->containsIdentical($value),
                'Failed asserting that the collection contains the given value.'
            );
        });
    }

    /**
     * @param \Illuminate\Foundation\Application $app
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
        Config::set('app.debug', 'true');
        Config::set('app.key', 'base64:2+SetJaztC7g0a1sSF81LYsDasiWymO6tp8yVv6KGrA=');
        Config::set('database.default', 'testing');
        Config::set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
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
     *
     * @return void
     */
    protected function createSupers()
    {
        foreach ($this->supers as $super) {
            /** @var \Aviator\Helpdesk\Tests\Feature\Http\Dashboard\Acceptance\Tickets\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Tickets\User $user */
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
     *
     * @return void
     */
    protected function setAlternateTablesInConfig($app)
    {
        $prefix = 'hd_';

        Config::set('helpdesk.tables.users', 'users');
        Config::set('helpdesk.tables.tickets', $prefix . 'tickets');
        Config::set('helpdesk.tables.agents', $prefix . 'agents');
        Config::set('helpdesk.tables.agent_team', $prefix . 'agent_team');
        Config::set('helpdesk.tables.actions', $prefix . 'actions');
        Config::set('helpdesk.tables.generic_contents', $prefix . 'generic_contents');
        Config::set('helpdesk.tables.assignments', $prefix . 'assignments');
        Config::set('helpdesk.tables.due_dates', $prefix . 'due_dates');
        Config::set('helpdesk.tables.replies', $prefix . 'replies');
        Config::set('helpdesk.tables.teams', $prefix . 'teams');
        Config::set('helpdesk.tables.team_assignments', $prefix . 'team_assignments');
        Config::set('helpdesk.tables.closings', $prefix . 'closings');
        Config::set('helpdesk.tables.openings', $prefix . 'openings');
        Config::set('helpdesk.tables.notes', $prefix . 'notes');
        Config::set('helpdesk.tables.collaborators', $prefix . 'collaborators');
    }

    protected function withoutErrorHandling()
    {
        app()->instance(ExceptionHandler::class, new class() extends Handler
        {
            public function __construct()
            {
            }

            public function report(Throwable $e)
            {
            }

            public function render($request, Throwable $e)
            {
                throw $e;
            }
        });
    }
}
