<?php

namespace Larapress\Profiles\Providers;

use Illuminate\Support\ServiceProvider;
use Larapress\CRUD\Services\CRUD\ICRUDExporter;
use Larapress\CRUD\Services\CRUD\ICRUDFilterStorage;
use Larapress\Profiles\Base\BaseCRUDFilterStorage;
use Larapress\Profiles\Base\BaseCRUDQueryExport;
use Larapress\Profiles\Commands\CreateDomain;
use Larapress\Profiles\Commands\ExportForms;
use Larapress\Profiles\Commands\ImportForms;
use Larapress\Profiles\Repository\Domain\DomainRepository;
use Larapress\Profiles\Repository\Domain\IDomainRepository;
use Larapress\Profiles\Repository\Form\FormRepository;
use Larapress\Profiles\Repository\Form\IFormRepository;
use Larapress\Profiles\Repository\PhoneNumber\IPhoneNumberRepository;
use Larapress\Profiles\Repository\PhoneNumber\PhoneNumberRepository;
use Larapress\Profiles\Repository\Settings\ISettingsRepository;
use Larapress\Profiles\Repository\Settings\SettingsRepository;
use Larapress\Profiles\Repository\User\IUserRepository;
use Larapress\Profiles\Repository\User\UserRepository;
use Larapress\Profiles\Services\FormEntry\FormEntryService;
use Larapress\Profiles\Services\FormEntry\IFormEntryService;
use Larapress\Profiles\Services\ProfileUser\IProfileUserServices;
use Larapress\Profiles\Services\ProfileUser\ProfileUserServices;
use Larapress\Profiles\Services\Settings\ISettingsService;
use Larapress\Profiles\Services\Settings\SettingsService;
use Larapress\Profiles\Validations\DomainValidator;
use Larapress\Profiles\Validations\IPAddressListValidator;
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
        $this->app->bind(ISettingsRepository::class, SettingsRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IPhoneNumberRepository::class, PhoneNumberRepository::class);
        $this->app->bind(IFormRepository::class, FormRepository::class);
        $this->app->bind(IFormEntryService::class, FormEntryService::class);
        $this->app->bind(ISettingsService::class, SettingsService::class);
        $this->app->bind(IProfileUserServices::class, ProfileUserServices::class);

        $this->app->bind(ICRUDFilterStorage::class, BaseCRUDFilterStorage::class);
        $this->app->bind(ICRUDExporter::class, BaseCRUDQueryExport::class);

        $this->app->register(EventServiceProvider::class);
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
        IPAddressListValidator::register();
        DomainValidator::register();

        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'larapress');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        $this->publishes([
            __DIR__.'/../../config/profiles.php' => config_path('larapress/profiles.php'),
        ], ['config', 'larapress', 'larapress-profiles']);


        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateDomain::class,
                ExportForms::class,
                ImportForms::class,
            ]);
        }
    }
}
