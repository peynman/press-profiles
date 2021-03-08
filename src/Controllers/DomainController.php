<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\DomainCRUDProvider;

class DomainController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.domains.name'),
            self::class,
            DomainCRUDProvider::class
        );
    }
}
