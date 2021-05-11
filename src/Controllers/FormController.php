<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\Services\CRUD\BaseCRUDController;
use Larapress\Profiles\CRUD\FormCRUDProvider;

/**
 * Standard CRUD Controller for Form resource.
 *
 * @group Forms Management
 */
class FormController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        parent::registerCrudRoutes(
            config('larapress.profiles.routes.forms.name'),
            self::class,
            FormCRUDProvider::class
        );
    }
}
