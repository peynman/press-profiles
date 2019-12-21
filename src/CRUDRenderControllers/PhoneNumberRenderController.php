<?php

namespace Larapress\Profiles\CRUDRenderControllers;

use Larapress\CRUDRender\Base\BaseCRUDRenderController;
use Larapress\CRUDRender\Base\ICRUDRenderProvider;
use Larapress\Profiles\MetaData\PhoneNumberMetaData;

class PhoneNumberRenderController extends BaseCRUDRenderController
{
    public static function registerRoutes()
    {
        parent::registerRenderRoutes(
            config('larapress.profiles.routes.phone-numbers.name'),
            self::class,
            PhoneNumberMetaData::class,
            ICRUDRenderProvider::class
        );
    }
}
