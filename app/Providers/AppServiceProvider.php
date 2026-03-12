<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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

        // Force HTTPS in production, but never for local artisan serve hosts.
        if (! $this->app->runningInConsole()) {
            $host = request()->getHost();
            $isLocalHost = in_array($host, ['127.0.0.1', 'localhost', '::1'], true);

            if (app()->environment('production') && ! $isLocalHost && str_starts_with(config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }
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
