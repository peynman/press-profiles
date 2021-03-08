<?php

namespace Larapress\Profiles\Repository\User;

use Larapress\Profiles\Flags\UserFlags;

class UserRepository implements IUserRepository
{

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return array
     */
    public function getUserFlags($user)
    {
        return UserFlags::toArray();
    }
}
