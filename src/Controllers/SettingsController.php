<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\SettingsCRUDProvider;

/**
 * Standard CRUD Controller for Settings resource.
 *
 * @group User Settings Management
 */
class SettingsController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.settings.name'),
            self::class,
            config('larapress.profiles.routes.settings.provider'),
            [
                'store.duplicate' => [
                    'methods' => ['POST'],
                    'uses' => '\\' . self::class . '@store',
                    'url' => config('larapress.profiles.routes.settings.name').'/duplicate',
                ],
            ]
        );
    }
}
