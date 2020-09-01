<?php

namespace Larapress\Profiles\Services\Settings;

use Larapress\Profiles\IProfileUser;

class SettingsService implements ISettingsService {

    /**
     * Undocumented function
     *
     * @param string $key
     * @param object $value
     * @param string|null $type
     * @return Settings
     */
    public function updateGlobalSettings(string $key, $value, $type) {

    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param IProfileUser $user
     * @param object $value
     * @param string|null $type
     * @return Settings
     */
    public function updateUserSettings(string $key, IProfileUser $user, $value, $type) {

    }
}
