<?php

namespace Larapress\Profiles\Base;

use Larapress\CRUD\Base\ICRUDFilterStorage;
use Larapress\CRUD\Base\ICRUDProvider;
use Larapress\Profiles\Models\Settings;

class BaseCRUDFilterStorage implements ICRUDFilterStorage
{
    /**
     * @param string $key
     * @param array|null $value
     * @param string $userId
     */
    public function putFilters(string $key, array $value, string $userId)
    {
        Settings::putSettings($key, $value, $userId);
    }

    /**
     * @param string $key
     * @param array $defaultValue
     * @param string $userId
     *
     * @return array|null|string
     */
    public function getFilters(string $key, array $defaultValue, string $userId)
    {
        return Settings::getSettings($key, $defaultValue, $userId);
    }

    /**
     * @param string $sessionId
     * @param string $providerClass
     * @return string
     */
    public function getFilterKey(string $sessionId, string $providerClass)
    {
        return 'filters.'.$providerClass.'.'.$sessionId;
    }

    /**
     * @param string $sessionId
     * @param ICRUDProvider $provider
     * @return string[]|array|null
     */
    public function getFilterValues(string $sessionId, ICRUDProvider $provider)
    {
        $providerName = class_basename($provider);
        $filtersKey = $this->getFilterKey($sessionId, $providerName);
        $values = Settings::getSettings($filtersKey, null, auth()->guest() ? null: auth()->user()->id);
        $defaults = $provider->getFilterDefaultValues();
        return array_merge($defaults, is_array($values) ? $values: []);
    }
}
