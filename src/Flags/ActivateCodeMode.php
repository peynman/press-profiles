<?php

namespace Larapress\Profiles\Flags;

use Larapress\Core\BaseType;

class ActivateCodeMode
{
    use BaseType;

    const BROWSER_DESKTOP = 'BROWSER_DESKTOP';
    const BROWSER_MOBILE = 'BROWSER_MOBILE';

    public static function toArray()
    {
        return [
            [
                'id' => self::BROWSER_DESKTOP,
                'title' => trans('models.activate-codes.modes')[self::BROWSER_DESKTOP],
            ],
            [
                'id' => self::BROWSER_MOBILE,
                'title' => trans('models.activate-codes.modes')[self::BROWSER_MOBILE],
            ],
        ];
    }
}
