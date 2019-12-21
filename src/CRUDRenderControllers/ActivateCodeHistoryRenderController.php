<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\ActivateCodeHistoryMetaData;

class ActivateCodeHistoryRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        $name = config('larapress.profiles.routes.activate-codes-history.name');
        parent::registerRenderRoutes(
            $name,
            self::class,
            ActivateCodeHistoryMetaData::class,
            ICRUDRenderProvider::class // use default view provider
        );
    }
}
