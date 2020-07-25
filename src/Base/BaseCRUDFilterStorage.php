<?php

namespace Larapress\Profiles\Base;

use Illuminate\Support\Str;
use Larapress\CRUD\Services\ICRUDFilterStorage;
use Larapress\CRUD\Services\ICRUDProvider;
use Larapress\Profiles\Repository\Settings\ISettingsRepository;

class BaseCRUDFilterStorage implements ICRUDFilterStorage
{
    /**
     * @var \Larapress\Profiles\Repository\Settings\ISettingsRepository
     */
    private $settingsRepository;

    /**
     * BaseCRUDFilterStorage constructor.
     *
     * @param \Larapress\Profiles\Repository\Settings\ISettingsRepository $settingsRepository
     */
    public function __construct(ISettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param string $key
     * @param array|null $value
     * @param string $userId
     */
    public function putFilters(string $key, $value, string $userId)
    {
        $this->settingsRepository->put($key, 'crud', $value, $userId);
    }

    /**
     * @param string $key
     * @param array|null $defaultValue
     * @param string $userId
     *
     * @return array|null|string
     */
    public function getFilters(string $key, $defaultValue, string $userId)
    {
        return $this->settingsRepository->get($key, 'crud', $defaultValue, $userId);
    }

    /**
     * @param string $sessionId
     * @param ICRUDProvider $provider
     * @return string[]|array|null
     */
    public function getFilterValues(string $sessionId, ICRUDProvider $provider)
    {
        $providerName = get_class($provider);
        $filtersKey = $this->getFilterKey($sessionId, $providerName);
        $values = $this->settingsRepository->get($filtersKey, 'crud',null, auth()->guest() ? null : auth()->user()->id);
        $defaults = $provider->getFilterDefaultValues();

        return array_merge($defaults, is_array($values) ? $values : []);
    }

    /**
     * @param string $sessionId
     * @param string $providerClass
     * @return string
     */
    public function getFilterKey(string $sessionId, string $providerClass)
    {
        return 'filters.'.Str::lower(str_replace('\\', '.', $providerClass)).'.'.Str::lower($sessionId);
    }
}
