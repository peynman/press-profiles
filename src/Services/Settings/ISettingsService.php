<?php

namespace Larapress\Profiles\Services\Settings;

use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

interface ISettingsService {
    /**
     * Undocumented function
     *
     * @param string $key
     * @param object $value
     * @param string|null $type
     * @return Settings
     */
    public function updateGlobalSettings(string $key, $value, $type);

    /**
     * Undocumented function
     *
     * @param string $key
     * @param IProfileUser $user
     * @param object $value
     * @param string|null $type
     * @return Settings
     */
    public function updateUserSettings(string $key, IProfileUser $user, $value, $type);


    /**
     * Undocumented function
     *
     * @param Domain $domain
     * @return void
     */
    public function applyGlobalSettingsForDomain($domain);

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return void
     */
    public function applyUserSettings(IProfileUser $user);

}
