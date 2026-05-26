<?php

namespace Leaptel\Laravel;

use Leaptel\Commands\TestCommand;

class EmbeddedServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestCommand::class,
            ]);
        }
        $this->loadMigrationsFrom(__DIR__ . "/migrations");
    }
}
