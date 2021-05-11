<?php


namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\FilterCRUDProvider;

/**
 * Standard CRUD Controller for Filter resource.
 *
 * @group Filters Management
 */
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
