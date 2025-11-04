<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\SyncQueueCommand;
use App\Models\OnsiteRequest;
use App\Models\StudentRequest;
use App\Observers\OnsiteRequestObserver;
use App\Observers\StudentRequestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register queue management service
        $this->app->singleton(\App\Services\QueueManagementService::class);

        // Register OneSignal notification service
        $this->app->singleton(\App\Services\OneSignalNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the OnsiteRequest observer
        OnsiteRequest::observe(OnsiteRequestObserver::class);

        // Register the StudentRequest observer
        StudentRequest::observe(StudentRequestObserver::class);

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
