<?php


namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\FilterCRUDProvider;

class FilterController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.filters.name'),
            self::class,
            FilterCRUDProvider::class
        );
    }
}
