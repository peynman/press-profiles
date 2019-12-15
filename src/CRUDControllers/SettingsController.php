<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Controllers\BaseCRUDController;
use Larapress\Profiles\CRUD\SettingsCRUDProvider;
use Larapress\Profiles\CRUD\SettingsDuplicateDomainProvider;

class SettingsController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        $name = config('larapress.profiles.routes.settings.name');
        parent::registerCrudRoutes(
            $name,
            self::class,
            SettingsCRUDProvider::class
        );

        parent::registerCRUDVerbs(
            $name,
            [
                'store.duplicate' => [
                    'methods' => ['POST'],
                    'uses' => '\\'.self::class.'@store',
                    'url' => $name.'/duplicate',
                ],
            ],
            SettingsDuplicateDomainProvider::class
        );
    }
}
