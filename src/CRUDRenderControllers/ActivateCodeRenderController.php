<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\CRUD\ActivateCodeCRUDProvider;
use Larapress\Profiles\MetaData\ActivateCodeMetaData;

class ActivateCodeRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.activate-codes.name'),
            self::class,
            ActivateCodeMetaData::class,
            ICRUDRenderProvider::class // use default view provider
        );
    }
}
