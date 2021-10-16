<?php

namespace Larapress\Profiles\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // activity log listener for events
        'Larapress\CRUD\Events\CRUDUpdated' => [
            'Larapress\Profiles\Services\ActivityLog\ActivityLogListener',
        ],
        'Larapress\CRUD\Events\CRUDCreated' => [
            'Larapress\Profiles\Services\ActivityLog\ActivityLogListener',
        ],
        'Larapress\CRUD\Events\CRUDDeleted' => [
            'Larapress\Profiles\Services\ActivityLog\ActivityLogListener',
        ],
        'Larapress\Auth\Signin\SigninEvent' => [
            'Larapress\Profiles\Services\DeviceMonitor\SigninEventListener',
        ]
    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
