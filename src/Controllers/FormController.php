<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\CRUDController;

/**
 * Standard CRUD Controller for Form resource.
 *
 * @group Forms Management
 */
class FormController extends CRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.forms.name'),
            self::class,
            config('larapress.profiles.routes.forms.provider'),
        );
    }
}
