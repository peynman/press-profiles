<?php

namespace Larapress\Profiles\Flags;

use Larapress\Core\BaseFlags;

class UserFlags
{
    use BaseFlags;

    const VERIFIED_USER = 1;
    const VERIFICATION_INFO_SENT = 2;
    const BANNED = 4;

    const MAXVALUE = 4;

    public static function getTitle($flag)
    {
        return self::__getTitle($flag, 'larapress::models.user.flags');
    }
}
