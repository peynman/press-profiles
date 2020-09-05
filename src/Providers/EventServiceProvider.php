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
        'Larapress\Profiles\Services\FormEntry\FormEntryUpdateEvent' => [
            'Larapress\Profiles\Services\FormEntry\FormEntryUpdateReport',
        ],
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
