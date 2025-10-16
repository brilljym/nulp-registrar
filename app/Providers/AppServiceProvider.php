<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\SyncQueueCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register queue management service
        $this->app->singleton(\App\Services\QueueManagementService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncQueueCommand::class,
                \App\Console\Commands\TestQueuePusherCommand::class,
            ]);
        }
    }
}
