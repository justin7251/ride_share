<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TripStarted;
use App\Events\TripCompleted;
use App\Events\TripLocationUpdated;
use App\Events\TripAccepted;

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
        TripStarted::class => [
            // Add listeners here if needed
        ],
        TripCompleted::class => [
            // Add listeners here if needed
        ],
        TripLocationUpdated::class => [
            // Add listeners here if needed
        ],
        TripAccepted::class => [
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
