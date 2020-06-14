<?php

namespace Larapress\Profiles\Repository\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Larapress\Profiles\Models\Settings;

class SettingsRepository implements ISettingsRepository
{
    /**
     * @param \Larapress\Profiles\Models\Settings $settings
     * @return bool|null|void
     * @throws \Exception
     */
    public function delete(Settings $settings)
    {
        self::forgetFromCache($settings->key, $settings->user_id);
        $settings->delete();
    }

    /**
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param int $user_id
     *
     * @return string
     */
    public function put(string $key, string $type, $value, $user_id = null)
    {
        Cache::put(self::getCacheKeyName($key, $user_id), $value, 60);
        $val = $value;
        if (! is_string($val)) {
            $val = json_encode($value);
        }

        Settings::updateOrCreate([
                'key' => $key,
                'type' => $type,
            ], [
                'val' => $val,
                'user_id' => (is_null($user_id) ? null : $user_id),
                'author_id' => Auth::user()->id,
        ]);

        return $value;
    }

    /**
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param int $user_id
     *
     * @return string|array|null
     */
    public function get(string $key, string $type, $value = null, $user_id = null)
    {
        $cached = Cache::get($this->getCacheKeyName($key, $user_id), null);
        if (is_null($cached)) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = Settings::where('key', $key);
            if (! is_null($user_id)) {
                $query->where('user_id', $user_id);
            }
            $db_value = $query->first();
            if (! is_null($db_value)) {
                Cache::put($this->getCacheKeyName($key, $user_id), $db_value->val, null);
                $val = $db_value->val;
                if (is_string($val)) {
                    $json_val = json_decode($val, true);
                    if (! is_null($json_val) || $val == 'null') {
                        return $json_val;
                    }
                }

                return $val;
            } else {
                if (! is_null($value)) {
                    return $this->put($key, $value, $user_id);
                }
            }
        } else {
            if (is_string($cached)) {
                $cached_value = json_decode($cached);
                if (! is_null($cached_value)) {
                    return $cached_value;
                }
            }

            return $cached;
        }

        return $value;
    }

    /**
     * @param string $key
     * @param $type
     * @param mixed $value
     * @param int $user_id
     * @param callable $closure
     *
     * @return string
     */
    public function update($key, $type, $value = null, $user_id = null, $closure = null)
    {
        $v = $this->get($key, $value, $user_id);
        if (! is_null($closure) && is_callable($closure)) {
            $v = $closure($v);
        }
        return $this->put($key, $v, $user_id);
    }

    /**
     * @param string        $key
     * @param int|null  $user_id
     */
    public function forgetFromCache($key, $user_id = null)
    {
        Cache::forget($this->getCacheKeyName($key, $user_id));
    }

    /**
     * @param string        $key
     * @param int|null  $user_id
     *
     * @return string
     */
    protected function getCacheKeyName($key, $user_id = null)
    {
        return 'settings:'.((is_null($user_id) ? '' : $user_id.':')).$key;
    }
}
