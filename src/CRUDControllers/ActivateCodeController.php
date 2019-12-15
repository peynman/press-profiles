<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Controllers\BaseCRUDController;
use Larapress\Profiles\CRUD\ActivateCodeCRUDProvider;

class ActivateCodeController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.activate-codes.name'),
            self::class,
            ActivateCodeCRUDProvider::class
        );
    }
}
