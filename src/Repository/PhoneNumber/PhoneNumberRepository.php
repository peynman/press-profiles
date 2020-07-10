<?php

namespace Larapress\Profiles\Repository\PhoneNumber;

use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\PhoneNumber;

class PhoneNumberRepository implements IPhoneNumberRepository {
    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return array
     */
    public function getPhoneNumberFlags(IProfileUser $user) {
        $labels = trans('larapress::models.phone-numbers.flags');
        return [
            [
                'id' => PhoneNumber::FLAGS_VERIFIED,
                'title' => $labels[PhoneNumber::FLAGS_VERIFIED],
            ],
            [
                'id' => PhoneNumber::FLAGS_DO_NOT_CONTACT,
                'title' => $labels[PhoneNumber::FLAGS_DO_NOT_CONTACT],
            ],
        ];
    }
}
