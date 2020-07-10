<?php

namespace Larapress\Profiles\Repository\PhoneNumber;

use Larapress\Profiles\IProfileUser;

interface IPhoneNumberRepository {
    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return array
     */
    public function getPhoneNumberFlags(IProfileUser $user);
}
