<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\RideStarted;
use App\Events\RideCompleted;
use App\Events\RideLocationUpdated;
use App\Events\RideAccepted;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RideStarted::class => [
            // Add listeners here if needed
        ],
        RideCompleted::class => [
            // Add listeners here if needed
        ],
        RideLocationUpdated::class => [
            // Add listeners here if needed
        ],
        RideAccepted::class => [
            // Add listeners here if needed
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
