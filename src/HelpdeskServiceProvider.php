<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Commands\CreateSuper;
use Illuminate\Support\ServiceProvider;

class HelpdeskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');

        $this->publishConfig();
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/helpdesk.php',
            'helpdesk'
        );

        $this->app->register(ObserversProvider::class);
        $this->app->register(MiddlewaresProvider::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'helpdesk');
        $this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');

        $this->publishFactories();
        $this->publishImages();

        $this->setBladeDirectives();

        $this->commands([
            CreateSuper::class,
        ]);

        $this->app->register(NotificationsProvider::class);
        $this->app->register(RepositoriesProvider::class);
    }

    /**
     * Make the configuration file available for publishing.
     * @return void
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/helpdesk.php' => config_path('helpdesk.php'),
        ], 'config');
    }

    /**
     * Make the helpdesk factory available for publishing.
     * @return void
     */
    protected function publishFactories()
    {
        $this->publishes([
            __DIR__ . '/../resources/factories/HelpdeskFactory.php' => database_path('factories/HelpdeskFactory.php'),
        ], 'factories');
    }

    /**
     * Publish avatar images.
     * @return void
     */
    protected function publishImages()
    {
        $this->publishes([
            __DIR__ . '/../resources/images' => public_path('vendor/aviator'),
        ], 'public');
    }

    /**
     * Publish blade directives.
     * @return void
     */
    protected function setBladeDirectives()
    {
        app('blade.compiler')->directive('para', function ($var) {
            return "<?php echo nl2br($var); ?>";
        });
    }
}
