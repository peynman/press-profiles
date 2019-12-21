<?php


namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\FilterMetaData;

class FilterRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.filters.name'),
            self::class,
            FilterMetaData::class,
            ICRUDRenderProvider::class
        );
    }
}
