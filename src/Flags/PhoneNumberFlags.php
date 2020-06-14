<?php

namespace Larapress\Profiles\Flags;

use Larapress\CRUD\BaseFlags;

class PhoneNumberFlags
{
    use BaseFlags;

    const PRIMARY = 1;
    const VERIFIED = 2;
    const DO_NOT_CONTACT = 4;
    const DO_NOT_LOGIN = 8;

    const MAXVALUE = 8;

    public static function getTitle($flag)
    {
        return self::__getFlagProperty($flag, 'models.phone-number.flags');
    }
}
