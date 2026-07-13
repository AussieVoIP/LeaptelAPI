<?php

namespace Leaptel\Laravel;

use Illuminate\Support\Facades\Route;
use Leaptel\Commands\CleanupServicesCommand;
use Leaptel\Commands\Ctags;
use Leaptel\Commands\LVCAdd;
use Leaptel\Commands\LVCModify;
use Leaptel\Commands\RunWebhook;
use Leaptel\Commands\ServiceOrderCommand;
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
                ServiceOrderCommand::class,
            ]);
            $this->loadMigrationsFrom(__DIR__ . "/migrations");
        }
        // We need to wrap this in the web middleware so it knows to auth.
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . "/routes/leaptel.php");
        });
        $this->loadViewsFrom(__DIR__ . "/views", "leaptel");
    }
}
