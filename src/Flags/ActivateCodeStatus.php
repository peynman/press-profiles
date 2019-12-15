<?php

namespace Larapress\Profiles\Flags;

use Larapress\Core\BaseType;

class ActivateCodeStatus
{
    use BaseType;

    const NOT_USED = 1;
    const USED = 2;

    const MINVALUE = 1;
    const MAXVALUE = 2;

    public static function getTitle($flag)
    {
        return self::__getTitle('models.activate-codes.status', $flag);
    }
}
