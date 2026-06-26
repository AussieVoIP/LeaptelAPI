<?php

namespace Leaptel\Laravel;

use Leaptel\Commands\CleanupServicesCommand;
use Leaptel\Commands\Ctags;
use Leaptel\Commands\LVCAdd;
use Leaptel\Commands\LVCModify;
use Leaptel\Commands\RunWebhook;
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
                CleanupServicesCommand::class,
                LVCAdd::class,
                LVCModify::class,
                Ctags::class,
                RunWebhook::class,
            ]);
            $this->loadMigrationsFrom(__DIR__ . "/migrations");
        }
    }
}
