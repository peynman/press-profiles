<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Larapress\Core\Extend\Helpers;
use Larapress\Profiles\Models\Domain;
use Larapress\CRUD\ICRUDUser;

/**
 * Class PhoneNumber
 *
 * @property int            $id
 * @property int            $user_id
 * @property int            $domain_id
 * @property string         $number
 * @property string         $type
 * @property string         $desc
 * @property int            $flags
 * @property ICRUDUser      $user
 * @property Domain         $domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 *
 * @package Larapress\CRUD\Models
 */
class PhoneNumber extends Model
{
    use SoftDeletes;

    protected $table = 'phone_numbers';

    public $fillable = [
        'user_id',
        'domain_id',
        'number',
        'type',
        'flags',
        'desc',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            config('larapress.crud.user.class'),
            'user_id',
            'id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    /**
     * @param        $args
     * @param array  $keys
     *
     * @return mixed
     */
    public static function processNumbers($args, $keys = ['numbers'])
    {
        foreach ($keys as $key) {
            if (isset($args[$key])) {
                $numbers_array = [];
                if (is_string($args[$key])) {
                    $numbers_array = json_decode($args[$key]);
                    if (is_null($numbers_array)) {
                        $numbers_array = [$args[$key]];
                    }
                    if (is_string($numbers_array)) {
                        $numbers_array = [$numbers_array];
                    }
                } elseif (is_array($args[$key])) {
                    $numbers_array = $args[$key];
                }

                $numeric_array = [];
                foreach ($numbers_array as $number) {
                    $phoneNumber = Helpers::enNumbers($number);
                    if (Str::startsWith($number, ['0', '۰'])) {
                        $phoneNumber = '0'.$phoneNumber;
                    }
                    $numeric_array[] = $phoneNumber;
                }
                $numbers_array = $numeric_array;
                /** @var PhoneNumber[] $numbers */
                $numbers = PhoneNumber::whereIn('number', $numbers_array)->get();
                if (count($numbers) < count($numbers_array)) {
                    foreach ($numbers_array as $new_number) {
                        $new = true;
                        foreach ($numbers as $number) {
                            if ($new_number == $number->number) {
                                $new = false;
                                break;
                            }
                        }

                        if ($new) {
                            $created = PhoneNumber::create([
                                'number' => $new_number,
                                'flags' => 0,
                            ]);
                            $numbers[] = $created;
                        }
                    }
                }
                $args[$key] = $numbers;
            }
        }

        return $args;
    }
}
