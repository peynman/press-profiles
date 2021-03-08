<?php

namespace Larapress\Profiles\Services\Settings;

use Illuminate\Database\Eloquent\Builder;
use Larapress\Profiles\IProfileUser;
use Larapress\Profiles\Models\Domain;
use Larapress\Profiles\Models\Settings;

class SettingsService implements ISettingsService
{

    /**
     * Undocumented function
     *
     * @param string $key
     * @param object $value
     * @param string|null $type
     * @return Settings
     */
    public function updateGlobalSettings(string $key, $value, $type)
    {
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
    public function updateUserSettings(string $key, IProfileUser $user, $value, $type)
    {
    }

    /**
     * Undocumented function
     *
     * @param Domain $domain
     * @return void
     */
    public function applyGlobalSettingsForDomain($domain)
    {
        $query = Settings::query()
            ->where('type', 'config')
            ->whereNull('user_id');
        if (!is_null($domain)) {
            $query->whereHas('domains', function (Builder $q) use ($domain) {
                $q->where('id', $domain->id);
            });
        }

        $configs = $query->get();
        $overrides = [];
        foreach ($configs as $config) {
            $overrides[$config->key] = $config->val;
        }
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
        $query = Settings::query()
            ->where('type', 'config')
            ->where('user_id', '=', $user->id);

        $configs = $query->get();
        $overrides = [];
        foreach ($configs as $config) {
            $overrides[$config->key] = $config->val;
        }
        config($overrides);
    }
}
