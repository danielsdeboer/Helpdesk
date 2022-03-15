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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\Assert;

abstract class TestCase extends Orchestra
{
    protected Make $make;
    protected Get $get;

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

    public function setUp (): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../resources/factories');

        $this->setUpDatabase();

        $this->artisan(
            'migrate',
            [
                '--database' => 'testing',
            ]
        );

        $this->createSupers();

        Notification::fake();

        $this->make = new Make();
        $this->get = new Get();

        TestResponse::macro(
            'data',
            function ($key) {
                /* @noinspection PhpUndefinedFieldInspection */
                return $this->original->getData()[$key];
            }
        );

        TestResponse::macro(
            'assertActiveHeaderTab',
            function (string $activeTab) {
                $inactiveTabs = array_filter(
                    ['dashboard', 'tickets', 'admin'],
                    function ($item) use ($activeTab) {
                        return $item !== $activeTab;
                    }
                );

                /* @var TestResponse $this */
                $this->assertSee('id="header-tab-' . $activeTab . '-active"', false);

                foreach ($inactiveTabs as $tab) {
                    /* @var TestResponse $this */
                    $this->assertDontSee('id="header-tab-' . $tab . '-active"', false);
                }
            }
        );

//        TestResponse::macro(
//            'assertSeeInOrder',
//            function (array $values) {
//                $position = 0;
//
//                foreach ($values as $value) {
//                    $valuePosition = mb_strpos($this->getContent(), $value, $position);
//
//                    if ($valuePosition === false || $valuePosition < $position) {
//                        Assert::fail(
//                            'Failed asserting that \'' . $this->getContent() .
//                            '\' contains "' . $value . '" in specified order.'
//                        );
//                    }
//
//                    $position = $valuePosition + mb_strlen($value);
//                }
//            }
//        );

        TestResponse::macro(
            'assertSeeEncoded',
            function (string $value) {
                PHPUnit::assertStringContainsString(e($value), $this->getContent());

                return $this;
            }
        );

        Collection::macro(
            'assertContains',
            function ($value) {
                /* @noinspection PhpParamsInspection */
                Assert::assertTrue(
                    $this->contains($value),
                    'Failed asserting that the collection contains the given value.'
                );
            }
        );

        Collection::macro(
            'assertNotContains',
            function ($value) {
                /* @noinspection PhpParamsInspection */
                Assert::assertFalse(
                    $this->contains($value),
                    'Failed asserting that the collection does not contain the given value.'
                );
            }
        );
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders ($app)
    {
        return [
            HelpdeskServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp ($app)
    {
        $app['config']->set('app.debug', 'true');
        $app['config']->set('app.key', 'base64:2+SetJaztC7g0a1sSF81LYsDasiWymO6tp8yVv6KGrA=');
        $app['config']->set('database.default', 'testing');

        $app['config']->set(
            'database.connections.testing',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        if (isset($GLOBALS['altdb']) && $GLOBALS['altdb'] === true) {
            $this->setAlternateTablesInConfig($app);
        }

        Route::get(
            'login',
            function () {
                //
            }
        )->name('login');
    }

    protected function setUpDatabase ()
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
    protected function createSupers ()
    {
        foreach ($this->supers as $super) {
            /** @var \Aviator\Helpdesk\Tests\Feature\Http\Dashboard\Acceptance\Tickets\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Acceptance\Tickets\User $user */
            $user = User::query()->create(
                [
                    'name' => $super['name'],
                    'email' => $super['email'],
                ]
            );

            Agent::query()->create(
                [
                    'user_id' => $user->id,
                    'is_super' => 1,
                ]
            );
        }
    }

    /**
     * Set the ignored users array.
     * @return void
     */
    protected function addIgnoredUser (array $ignoredUsers)
    {
        Config::set('helpdesk.ignored', $ignoredUsers);
    }

    /**
     * Set alternate table names for testing that the database names
     * are properly variable everywhere.
     * @param $app
     * @return void
     */
    protected function setAlternateTablesInConfig ($app)
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
        app()->instance(
            ExceptionHandler::class,
            new class extends Handler
            {
                public function __construct ()
                {
                }

                public function report (\Throwable $e)
                {
                }

                public function render ($request, \Throwable $e)
                {
                    throw $e;
                }
            }
        );
    }
}
