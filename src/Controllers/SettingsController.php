<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
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

        parent::registerVerbs(
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
