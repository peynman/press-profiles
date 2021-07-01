<?php

namespace Larapress\Profiles\Services\Settings;

use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;

interface ISettingsService
{
    /**
     * Undocumented function
     *
     * @param integer $domainId
     * @param string $key
     * @param mixed $value
     * @param string $type
     *
     * @return Settings
     */
    public function updateDomainlSettings(int $domainId, string $key, $value, string $type);

    /**
     * Undocumented function
     *
     * @param string $key
     * @param IProfileUser $user
     * @param object $value
     * @param string $type
     *
     * @return Settings
     */
    public function updateUserSettings(IProfileUser $user, string $key, $value, string $type);


    /**
     * Undocumented function
     *
     * @param Domain $domain
     * @return void
     */
    public function applyDomainSettings(Domain $domain);

    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return void
     */
    public function applyUserSettings(IProfileUser $user);

    /**
     * Undocumented function
     *
     * @param int $userId
     *
     * @return void
     */
    public function forgetUserSettingsCache($userId);

    /**
     * Undocumented function
     *
     * @param int $domainId
     * @return void
     */
    public function forgetDomainSettingsCache($domainId);
}
