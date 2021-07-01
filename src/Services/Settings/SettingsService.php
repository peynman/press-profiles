<?php

namespace Larapress\Profiles\Services\Settings;

use Illuminate\Database\Eloquent\Builder;
use Larapress\CRUD\Extend\Helpers;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\Settings;

class SettingsService implements ISettingsService
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
    public function updateDomainlSettings(int $domainId, string $key, $value, string $type)
    {
        $this->forgetDomainSettingsCache($domainId);
    }

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
    public function updateUserSettings(IProfileUser $user, string $key, $value, string $type)
    {

    }

    /**
     * Undocumented function
     *
     * @param Domain $domain
     * @return void
     */
    public function applyDomainSettings(Domain $domain)
    {
        $overrides = Helpers::getCachedValue(
            'larapress.profiles.settings.domain.' . $domain->id,
            ['global_settings:' . $domain->id],
            86400,
            false,
            function () use ($domain) {
                $query = Settings::query()
                    ->where('type', 'config')
                    ->whereNull('user_id')
                    ->whereHas('domains', function (Builder $q) use ($domain) {
                        $q->where('id', $domain->id);
                    });

                $configs = $query->get();
                $overrides = [];
                foreach ($configs as $config) {
                    $overrides[$config->key] = $config->val;
                }

                return $overrides;
            }
        );

        config($overrides);
    }


    /**
     * Undocumented function
     *
     * @param IProfileUser $user
     * @return void
     */
    public function applyUserSettings(IProfileUser $user)
    {
        $overrides = Helpers::getCachedValue(
            'larapress.profiles.settings.user.' . $user->id,
            ['user_settings:' . $user->id],
            86400,
            false,
            function () use ($user) {
                $query = Settings::query()
                    ->where('type', 'config')
                    ->where('user_id', '=', $user->id);

                $configs = $query->get();
                $overrides = [];

                foreach ($configs as $config) {
                    $overrides[$config->key] = $config->val;
                }

                return $overrides;
            }
        );

        config($overrides);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function forgetUserSettingsCache($userId)
    {
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function forgetDomainSettingsCache($domainId)
    {
    }
}
