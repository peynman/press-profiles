<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\CRUD\ICRUDUser;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property int            $id
 * @property int            $user_id
 * @property int            $domain_id
 * @property int            $country_code
 * @property int            $city_code
 * @property int            $province_code
 * @property int            $postal_code
 * @property int            $flags
 * @property string         $address
 * @property array          $data
 * @property ICRUDUser      $user
 */
class PhysicalAddress extends Model
{
    use SoftDeletes;

    protected $table = 'physical_addresses';

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'domain_id',
        'country_code',
        'province_code',
        'city_code',
        'postal_code',
        'address',
        'flags',
        'data',
    ];

    public $casts = [
        'data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            config('larapress.crud.user.model'),
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
}
