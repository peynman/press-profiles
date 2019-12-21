<?php

namespace Larapress\Profiles\CRUDControllers;

use Larapress\CRUD\CRUDControllers\BaseCRUDController;
use Larapress\Profiles\CRUD\ActivateCodeHistoryCRUDProvider;

class ActivateCodeHistoryController extends BaseCRUDController
{
    public static function registerRoutes()
    {
        $name = config('larapress.profiles.routes.activate-codes-history.name');
        parent::registerCrudRoutes(
            $name,
            self::class,
            ActivateCodeHistoryCRUDProvider::class
        );

        parent::registerVerbs(
            $name,
            [
                'query.specific' => [
                    'methods' => ['POST'],
                    'uses' => '\\'.self::class.'@query',
                    'url' => $name.'/query/{activate_code_id}',
                ],
            ],
            ActivateCodeHistoryCRUDProvider::class
        );
    }

    /**
     * @return null|int
     */
    public static function getActivateCodeIDFromRequest()
    {
        if ((isset(\request()->route()->parameters['activate_code_id']))) {
            return \request()->route()->parameters['activate_code_id'];
        }
    }
}
