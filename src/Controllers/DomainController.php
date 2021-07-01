<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;
use Larapress\Profiles\CRUD\DomainCRUDProvider;

/**
 * Standard CRUD Controller for Domain resource.
 *
 * @group Domains Management
 */
class DomainController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.domains.name'),
            self::class,
            config('larapress.profiles.routes.domains.provider'),
        );
    }
}
