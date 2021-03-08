<?php

namespace Larapress\Profiles\Controllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\FormCRUDProvider;

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
