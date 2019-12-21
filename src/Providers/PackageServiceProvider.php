<?php

namespace Larapress\Profiles\Providers;

use Illuminate\Support\ServiceProvider;
use Larapress\CRUD\Base\ICRUDExporter;
use Larapress\CRUD\Base\ICRUDFilterStorage;
use Larapress\Profiles\Base\BaseCRUDFilterStorage;
use Larapress\Profiles\Base\BaseCRUDQueryExport;
use Larapress\Profiles\CRUD\ActivityLogCRUDProvider;
use Larapress\Profiles\CRUD\UserCRUDProvider;
use Larapress\Profiles\Repository\Domain\DomainRepository;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Larapress\Profiles\Validations\UniqueInDomainValidator;
use Larapress\Profiles\Validations\UniqueInMasterDomainValidator;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IDomainRepository::class, DomainRepository::class);
        $this->app->bind(ICRUDFilterStorage::class, BaseCRUDFilterStorage::class);
        $this->app->bind(ICRUDExporter::class, BaseCRUDQueryExport::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UniqueInMasterDomainValidator::register();
        UniqueInDomainValidator::register();

        $this->loadMigrationsFrom(__DIR__.'/../../migrations');

        $this->publishes([
            __DIR__.'/../../config/profiles.php' => config_path('larapress/profiles.php'),
        ], ['config', 'larapress', 'larapress-profiles']);
    }
}
