<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Larapress\CRUD\ICRUDUser;

/**
 * @property int            $id
 * @property string         $key
 * @property string         $val
 * @property string         $type
 * @property ICRUDUser      $user
 * @property int            $user_id
 * @property int            $domain_id
 * @property Domain         $domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Settings extends Model
{
    protected $table = 'settings';

    public $fillable = [
        'user_id',
        'domain_id',
        'key',
        'val',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('larapress.crud.user.class'), 'user_id');
    }

    /**
     * Remove this record from database and cache
     * @return bool|null|void
     * @throws \Exception
     */
    public function delete()
    {
        self::forgetFromCache($this->key, $this->user_id);
        parent::delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @param int $user_id
     *
     * @return string
     */
    public static function putSettings($key, $value, $user_id = null)
    {
        Cache::put(self::getCacheKeyName($key, $user_id), $value, 60);
        $val = $value;
        if (!is_string($val)) {
            $val = json_encode($value);
        }
        self::updateOrCreate(['key' => $key], ['val' => $val, 'user_id' => (is_null($user_id) ? null:$user_id)]);
        return $value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @param int  $user_id
     *
     * @return string|array|null
     */
    public static function getSettings($key, $value = null, $user_id = null)
    {
        $cached = Cache::get(self::getCacheKeyName($key, $user_id), null);
        if (is_null($cached)) {
            $query = self::where('key', $key);
            if (!is_null($user_id)) {
                $query->where('user_id', $user_id);
            }
            $db_value = $query->first();
            if (!is_null($db_value)) {
                Cache::put(self::getCacheKeyName($key, $user_id), $db_value->val, null);
                $val = $db_value->val;
                if (is_string($val)) {
                    $json_val = json_decode($val, true);
                    if (!is_null($json_val) || $val == "null") {
                        return $json_val;
                    }
                }
                return $val;
            } else {
                if (!is_null($value)) {
                    return self::putSettings($key, $value, $user_id);
                }
            }
        } else {
            if (is_string($cached)) {
                $cached_value = json_decode($cached);
                if (!is_null($cached_value)) {
                    return $cached_value;
                }
            }
            return $cached;
        }

        return $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $user_id
     * @param callable $closure
     */
    public static function updateSettings($key, $value = null, $user_id = null, $closure = null)
    {
        $v = self::getSettings($key, $value, $user_id);
        if (!is_null($closure) && is_callable($closure)) {
            $v = $closure($v);
        }
        self::putSettings($key, $v, $user_id);
    }

    /**
     * @param string        $key
     * @param integer|null  $user_id
     */
    public static function forgetFromCache($key, $user_id = null)
    {
        Cache::forget(self::getCacheKeyName($key, $user_id));
    }

    /**
     * @param string        $key
     * @param integer|null  $user_id
     *
     * @return string
     */
    protected static function getCacheKeyName($key, $user_id = null)
    {
        return 'settings:' . ( ( is_null($user_id) ? '' : $user_id . ':' ) ) . $key;
    }
}
