<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\DomainCRUDProvider;

/**
 * Standard CRUD Controller for Domain resource.
 *
 * @group Domains Management
 */
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
