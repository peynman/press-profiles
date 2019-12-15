<?php


namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\Base\ICRUDService;
use Larapress\CRUD\Controllers\BaseCRUDController;
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
