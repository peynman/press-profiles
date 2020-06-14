<?php

namespace Larapress\Profiles\Repository\Settings;

use Larapress\Profiles\Models\Settings;

interface ISettingsRepository
{
    /**
     * @param \Larapress\Profiles\Models\Settings $settings
     * @return bool|null|void
     * @throws \Exception
     */
    public function delete(Settings $settings);

    /**
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param int $user_id
     *
     * @return string
     */
    public function put(string $key, string $type, $value, $user_id = null);

    /**
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param int $user_id
     *
     * @return string|array|null
     */
    public function get(string $key, string $type, $value = null, $user_id = null);

    /**
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param int $user_id
     * @param callable $closure
     *
     * @return string
     */
    public function update(string $key, string $type, $value = null, $user_id = null, $closure = null);

    /**
     * @param string        $key
     * @param int|null  $user_id
     */
    public function forgetFromCache(string $key, $user_id = null);
}